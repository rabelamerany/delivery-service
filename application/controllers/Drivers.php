<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Drivers extends CI_Controller
{
	function __construct()
	{
		set_time_limit(1000);
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');

		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}

	function index()
	{
		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
		}

		$this->template->write('title', 'Drivers', TRUE);
		$this->template->write('header', 'Drivers');

		$crud = new grocery_CRUD();
		$crud->set_table('drivers');
		$crud->set_subject('Driver');

		$columns = ['driverFirstName', 'driverLastName', 'driver_address1', 'driver_address2', 'driver_city', 'phone_number'];
		$fields = ['driverFirstName', 'driverLastName', 'driver_address1', 'driver_address2', 'driver_city', 'driver_email', 'password', 'phone_number'];

		if (is_granted('Driver') && ($this->session->userdata()['user_id'] == $crud->getStateInfo()->primary_key)) {
			$crud->unset_list();
			$crud->unset_back_to_list();
		} elseif (!(is_granted('Admin') || is_granted('Dispatcher'))) {
			redirect('errors/error403');
		} else {
			$fields = array_merge($fields, array('external', "driver_coordinates", "ratePerOrder", "active", "last_paid"));
		}
		$fields[] = 'driver_availability';

		if ('read' == $crud->getState()) {
			$columns[] = 'driver_email';
			$columns[] = 'date_added';
		}

		$crud->columns($columns);
		$crud->fields($fields);
		$crud->display_as('driverFirstName', 'First Name')
			->display_as('driverLastName', 'Last Name')
			->display_as('driver_address1', 'Address line 1')
			->display_as('driver_address2', 'Address line 2')
			->display_as('driver_city', 'City')
			->display_as('driver_email', 'Email')
			->display_as('phone_number', 'Phone Number')
			->display_as('active', 'Status')
			->display_as('external', 'External')
			->display_as('driver_coordinates', 'Coordinates');

		$crud->callback_field('driver_availability', array($this, 'availability_field_addedit'));
		$crud->callback_read_field('driver_availability', array($this, 'availability_field_read'));

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}

	function availability_field_addedit($value, $primary_key)
	{
		$value = empty($value) ? '{"0":[],"1":[],"2":[],"3":[],"4":[],"5":[],"6":[]}' : $value;

		$html = '<div id="day-schedule"></div>';
		$html .= '<input id="field-driver_availability" class="form-control" name="driver_availability" type="hidden" value="' . htmlspecialchars($value) . '">';

		$this->template->write('javascript', '$("#day-schedule").dayScheduleSelector({});');
		$this->template->write('javascript', '$("#day-schedule").data(\'artsy.dayScheduleSelector\').deserialize(' . $value . ');');
		$this->template->write('javascript', '$("#day-schedule").on(\'selected.artsy.dayScheduleSelector\',function(e,selected){
            $("#field-driver_availability").val(JSON.stringify($("#day-schedule").data(\'artsy.dayScheduleSelector\').serialize()));
        })');

		return $html;
	}

	function availability_field_read($value, $primary_key)
	{
		$days = empty($value) ? json_decode('{"0":[],"1":[],"2":[],"3":[],"4":[],"5":[],"6":[]}', true) : json_decode($value, true);

		$weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		$literal_availability = $ranges = "";

		for ($i = 0; $i < 7; $i++, $ranges = "") {
			foreach ($days[$i] as $day) $ranges .= implode("-", $day) . " , ";
			$literal_availability .= "<b>" . $weekdays[$i] . ":</b> " . $ranges . "<br/>";
		}

		return $literal_availability;
	}

	function check_unpaid()
	{
		$settings = [];
		$rows = $this->db->get('settings')->result();
		foreach ($rows as $row) {
			$settings[$row->key] = $row->value;
		}

		$query = $this->db->query("
            SELECT drivers.`driverNumber`, drivers.`driverFirstName`, drivers.`driverLastName`, drivers.`ratePerOrder`, COUNT(driverNumber) as orders_count
            FROM drivers
            JOIN orders ON drivers.driverNumber = orders.order_driver_assigned
            WHERE orders.status = \"Delivered\" AND (
              (DATEDIFF(CURRENT_DATE, DATE(drivers.date_added)) >= 30 AND DATE(drivers.last_paid) IS NULL) OR
              (DATEDIFF(CURRENT_DATE, DATE(drivers.last_paid)) >= 30 AND DATE(drivers.last_paid) < DATE(orders.date_added))
            )
            GROUP BY driverNumber;
        ");

		$drivers = [];
		foreach ($query->result() as $row) {
			$drivers[] = ["$row->driverFirstName $row->driverLastName", $row->ratePerOrder * $row->orders_count, $row->driverNumber];
		}

		if (count($drivers) > 0) {
			$this->load->library('email');
			$this->email->from('payment@' . $_SERVER['HTTP_HOST'], $settings['app_name']);
			$this->email->to($settings['app_DriverPaymentNotificationEmail']);
			$this->email->subject("Driver Payment Notification");
			$message = "";
			foreach ($drivers as $driver) {
				$data['driver'] = $driver[0];
				$query = $this->db->query("
                    SELECT order_for, order_from, order_address1, order_address2, description, date_added
                    FROM orders
                    WHERE order_driver_assigned = " . $driver[2] . "
                    AND DATEDIFF(CURRENT_DATE,DATE(orders.date_added)) >= 30;
                ");
				$orders = $query->result();

				foreach ($orders as $order) {
					$data['orders'][] = [
						'client' => $order->order_for,
						'from' => $order->order_from,
						'address' => $order->order_address1 . ' ' . $order->order_address2,
						'description' => $order->description,
						'added' => $order->date_added
					];
				}

				$settings = $this->db->get('settings')->result();
				foreach ($settings as $setting) {
					$data[$setting->key] = $setting->value;
				}

				// load the library Html2pdf
				$this->load->library('Html2pdf');
				//Set folder to save PDF to
				$this->html2pdf->folder('./assets/pdfs/');
				//Set the filename to save/download as
				$this->html2pdf->filename('Total_Driver' . $driver[2] . '_' . time() . '.pdf');
				//Set the paper defaults
				$this->html2pdf->paper('a4', 'portrait');

				//Load html view
				$this->html2pdf->html($this->load->view('pdf/drivers_30_days', $data, true));

				if ($path = $this->html2pdf->create('save')) {
					$this->email->attach($path);
				}

				$message .= "Driver $driver[0] needs a payment of $driver[1] MAD.\n";
			}
			$this->email->message($message);


			if (!$this->email->send()) {
				die('error: mail not sent');
			}
		}

		echo "Success";
	}

	function weekly_driver_stats()
	{
		$query = $this->db->query("
            SELECT CONCAT(d.driverFirstName,' ',d.driverLastName) as DriverFullName, 
            COUNT(o.id_order) AS orders_count,
            d.driver_availability, 
            d.driver_email
            FROM `orders` o RIGHT JOIN drivers d
            ON o.order_driver_assigned = d.driverNumber
            WHERE d.active = 1 AND DATE(o.date_added) >= DATE(NOW()) - INTERVAL 7 DAY
            GROUP BY d.driverNumber
        ");

		$this->db->where('key', 'app_name');
		$setting = $this->db->get('settings')->row();
		$this->db->where('key', 'app_DriverPaymentNotificationEmail');
		$app_DriverPaymentNotificationEmail = $this->db->get('settings')->row();

		$message = "<b>Driver Orders / Availability:</b><br>";
		$drivers_emails = [];
		foreach ($query->result() as $driver) {
			$availability = 0;
			foreach (json_decode($driver->driver_availability, true) as $range) {
				if (0 < count($range)) {
					$sup = explode(':', $range[0][1]);
					$inf = explode(':', $range[0][0]);
					$availability += ($sup[0] * 3600 + $sup[1] * 60) - ($inf[0] * 3600 + $inf[1] * 60);
				}
			}

			$availability /= 3600;
			$message .= $driver->DriverFullName . " " . $driver->orders_count . " / $availability hrs. <br>";
			$drivers_emails[] = $driver->driver_email;
		}

		$today = new DateTime();
		$last_week = clone $today;
		$last_week->modify('-7 days');

		$this->load->library('email');
		$this->email->from('stats@' . $_SERVER['HTTP_HOST'], $setting->value);
		$this->email->to($drivers_emails);
		$this->email->bcc($app_DriverPaymentNotificationEmail->value);
		$this->email->subject("Stats from " . $last_week->format('d/m/Y H:i') . " to " . $today->format('d/m/Y H:i'));
		$this->email->set_mailtype("html");
		$this->email->message($message);
		$this->email->send(FALSE);

		die('Success');
	}

	function driver_cash_in()
	{
		if (!(is_granted('Admin') || is_granted('Controller'))) {
			redirect('errors/error403');
		}

		$this->template->write('title', 'Driver Cash IN', TRUE);
		$this->template->write('header', 'Driver Cash IN');
		$this->template->write('style', "");
		$this->template->write('javascript', "
            $('#form').on('submit', function (event) {
                event.preventDefault();
                var url = $(this).attr('action');
                var driver = $('#driver').val();
                var start = $('#start_date').val();
                var end = $('#end_date').val();
                console.log(start);
                console.log(end);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        driver: driver,
                        startDate: start,
                        endDate: end
                    }, success: function (response) {
                        $('#number_orders').html(response.number_orders);
                        $('#cash_in_amount').html(response.cash_in_amount);
                    }
                });
            });
        ");

		$query = $this->db->query("
            SELECT driverNumber, CONCAT(d.driverFirstName, ' ', d.driverLastName) as driverFullName
            FROM `drivers` d
            WHERE d.active = 1
            GROUP BY d.driverNumber
        ");
		$drivers = $query->result();

//        $drivers = $query->result();
//        highlight_string("<?php\n" . var_export($drivers, true)); exit();

		$this->template->write_view('content', 'tes/driver_cash_in', ['drivers' => $drivers], true);
		$this->template->render();
	}

	function driver_cash_in_data()
	{
		$data = [];
		if ((isset($_POST['driver']) && !empty($_POST['driver'])) && (isset($_POST['startDate']) && !empty($_POST['startDate'])) && (isset($_POST['endDate']) && !empty($_POST['endDate']))) {
			$driver = $_POST['driver'];
			$startDate = (new DateTime($_POST['startDate']))->format('Y-m-d');
			$endDate = (new DateTime($_POST['endDate']))->format('Y-m-d');

			$query = $this->db->query('
                SELECT COUNT(o.order_driver_assigned) AS number_orders, SUM(o.order_delivery_cost) AS cash_in_amount
                FROM `orders` o
                JOIN drivers ON drivers.driverNumber = o.order_driver_assigned
                JOIN customers ON customers.customerNumber = o.order_customer
                WHERE o.order_driver_assigned = "' . $driver . '"
                AND customers.type <> "Business"
                AND o.date_added BETWEEN "' . $startDate . ' 00:00:00" AND "' . $endDate . ' 23:59:59"
                GROUP BY o.order_driver_assigned;
            ');

			$data = [
				'number_orders' => empty($query->result()) ? 0 : $query->result()[0]->number_orders,
				'cash_in_amount' => empty($query->result()) ? 0 : $query->result()[0]->cash_in_amount,
			];
		}

		header('Content-Type: application/json');
		echo json_encode($data);
	}

	function externals_payment_due()
	{
		if (!(is_granted('Admin') || is_granted('Controller'))) {
			redirect('errors/error403');
		}

		$this->template->write('title', 'Externals Payment Due', TRUE);
		$this->template->write('header', 'Externals Payment Due');
		$this->template->write('style', "");
		$this->template->write('javascript', "
            $('#form').on('submit', function (event) {
                event.preventDefault();
                var url = $(this).attr('action');
                var driver = $('#driver').val();
                var start = $('#start_date').val();
                var end = $('#end_date').val();
                console.log(start);
                console.log(end);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        driver: driver,
                        startDate: start,
                        endDate: end
                    }, success: function (response) {
                        $('#number_orders').html(response.number_orders);
                        $('#payment_due').html(response.payment_due);
                    }
                });
            });
        ");

		$query = $this->db->query("
            SELECT driverNumber, CONCAT(d.driverFirstName, ' ', d.driverLastName) as driverFullName
            FROM `drivers` d
            WHERE d.external = 1
            GROUP BY d.driverNumber
        ");
		$drivers = $query->result();

		$this->template->write_view('content', 'tes/externals_payment_due', ['drivers' => $drivers], true);
		$this->template->render();
	}

	function externals_payment_due_data()
	{
		$data = [];
		if ((isset($_POST['driver']) && !empty($_POST['driver'])) && (isset($_POST['startDate']) && !empty($_POST['startDate'])) && (isset($_POST['endDate']) && !empty($_POST['endDate']))) {
			$driver = $_POST['driver'];
			$startDate = (new DateTime($_POST['startDate']))->format('Y-m-d');
			$endDate = (new DateTime($_POST['endDate']))->format('Y-m-d');

			$query = $this->db->query('
                SELECT COUNT(o.order_driver_assigned) AS number_orders, (SUM(o.driver_delivery_cost) - SUM(o.order_delivery_cost)) AS payment_due
                FROM `orders` o
                JOIN drivers ON drivers.driverNumber = o.order_driver_assigned
                WHERE o.order_driver_assigned = "' . $driver . '"
                AND drivers.external = 1
                AND o.date_added BETWEEN "' . $startDate . ' 00:00:00" AND "' . $endDate . ' 23:59:59"
                GROUP BY o.order_driver_assigned;
            ');

			$data = [
				'number_orders' => empty($query->result()) ? 0 : $query->result()[0]->number_orders,
				'payment_due' => empty($query->result()) ? 0 : $query->result()[0]->payment_due,
			];
		}

		header('Content-Type: application/json');
		echo json_encode($data);
	}
}
