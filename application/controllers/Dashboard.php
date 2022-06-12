<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 */
class Dashboard extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('general_helper');

        if (!is_user_logged_in()) {
            redirect('login');
        }

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');

        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index()
    {
        $this->template->write('title', 'Dashboard', TRUE);
        $this->template->write('header', 'Dashboard');
        $this->template->write_view('content', 'tes/dashboard', '', true);


        $this->template->write('style', "");
        $this->template->write('javascript', "
            $('[data-toggle=\"tooltip\"]').tooltip(); 
            dashboard(moment().subtract(15, 'days'), moment());
            $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                dashboard(picker.startDate, picker.endDate);
            });
		");

        $this->template->render();
    }

    function orderActivities()
    {
        if ((isset($_POST['startDate']) && !empty($_POST['startDate'])) && (isset($_POST['endDate']) && !empty($_POST['endDate']))) {
            $query = $this->db->query("SELECT DATE_FORMAT(date_added, '%Y-%m-%d') AS order_date, COUNT(*) AS order_count FROM orders WHERE date_added BETWEEN '" . $_POST['startDate'] . "' AND '" . $_POST['endDate'] . " 23:59:59' GROUP BY DATE_FORMAT(date_added, '%Y-%m-%d')");

            $data = [];
            $result = [];

            $begin = new DateTime($_POST['startDate']);
            $end = (new DateTime($_POST['endDate']))->modify('+1 day');
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            foreach ($period as $dt) {
                $result[$dt->getTimestamp()] = 0;
            }

            foreach ($query->result() as $row) {
                $result[strtotime($row->order_date)] = $row->order_count;
            }

            foreach ($result as $key => $value) {
                $data['chart'][] = [
                    'day' => $key * 1000,
                    'count' => $value + 0
                ];
            }

            $data['dispatcher'] = [];
            $query = $this->db->query("SELECT users.first_name, users.last_name, COUNT(orders.user_id) as order_count FROM `orders` JOIN users ON users.user_id = orders.user_id WHERE orders.date_added BETWEEN '" . $_POST['startDate'] . "' AND '" . $_POST['endDate'] . " 23:59:59' GROUP BY orders.user_id ORDER BY order_count DESC LIMIT 5;");
            if (0 < count($query->result())) {
                $bestScore = $query->result()[0]->order_count + 5;
                foreach ($query->result() as $row) {
                    $data['dispatcher'][] = [
                        'full_name' => "$row->first_name $row->last_name",
                        'count' => $row->order_count,
                        'score' => ($row->order_count / $bestScore) * 100
                    ];
                }
            }

            $data['driver'] = [];
            $query = $this->db->query("SELECT drivers.driverFirstName, drivers.driverLastName, COUNT(orders.order_driver_assigned) as order_count FROM `orders` JOIN drivers ON drivers.driverNumber = orders.order_driver_assigned WHERE orders.date_added BETWEEN '" . $_POST['startDate'] . "' AND '" . $_POST['endDate'] . " 23:59:59' GROUP BY orders.order_driver_assigned ORDER BY order_count DESC;");
            if (0 < count($query->result())) {
                $bestScore = $query->result()[0]->order_count;
                foreach ($query->result() as $row) {
                    $data['driver'][] = [
                        'full_name' => "$row->driverFirstName $row->driverLastName",
                        'count' => $row->order_count,
                        'score' => ($row->order_count / ($bestScore + 5)) * 100
                    ];
                }
            }

            $query = $this->db->query("SELECT *, CURRENT_TIMESTAMP FROM `orders` WHERE orders.status = \"New\" ORDER BY id_order DESC;");
            foreach ($query->result() as $row) {
                $data['activities'][] = [
                    'order' => $row->id_order,
                    'title' => "<strong>Order #$row->id_order:</strong> $row->order_address1, $row->order_address2, $row->order_city $row->order_zipcode",
                    'time' => ceil((strtotime($row->CURRENT_TIMESTAMP) - strtotime($row->date_added)) / 60),
                    'description' => strip_tags($row->description)
                ];
            }

            $today = new DateTime();
            $query = $this->db->query("SELECT * FROM `drivers` WHERE active = 1;");
            foreach ($query->result() as $row) {
                $days = json_decode($row->driver_availability, true);

                if (!empty($days[$today->format('w')])) {
                    foreach ($days[$today->format('w')] as $period) {
                        $from = new DateTime($period[0]);
                        $to = new DateTime($period[1]);

                        if ($from->getTimestamp() <= $today->getTimestamp() && $today->getTimestamp() < $to->getTimestamp()) {
                            $data['available'][] = ['name' => "$row->driverFirstName $row->driverLastName"];
                        }
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'data' => $data,
            ]);
        }
    }

}