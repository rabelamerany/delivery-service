<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Commandes de <?= $driver ?></title>
        <link rel="stylesheet" href="assets/css/paper.min.css"/>
        <link rel="stylesheet" href="assets/css/print.css"/>
    </head>
    <body>
        <header>
            <h1>Total des commandes de <?= $driver ?> dans les 30 derniers jours</h1>
        </header>
        <main>
            <?php $page = 1; $num_orders = count($orders);  ?>
            <?php $pages = $num_orders > 13 ? 1+ceil(($num_orders-10)/13) : 1; ?>
            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Client</th>
                        <th>From</th>
                        <th>To Destination</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php for ($i= 1, $count=1; $i <= $num_orders; $i++, $count++) {  ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $orders[$i-1]['client'] ?></td>
                        <td><?= $orders[$i-1]['from'] ?></td>
                        <td><?= $orders[$i-1]['address'] ?></td>
                        <td><?= $orders[$i-1]['description'] ?></td>
                        <td><?= $orders[$i-1]['added'] ?></td>
                    </tr>
                <?php if (($count == 10 && $i <= 13) || ($count%14 == 0 && $i < $num_orders)) { ?>
                </tbody>
            </table>
            <footer>
                Page <?= $page .' / '. $pages ?>
                <?php $count = 1; $page++; ?>
            </footer>
            <div style="page-break-inside:avoid; page-break-after:always;"></div>
            <table>
                <tbody><?php } } ?></tbody>
            </table>
        </main>
        <footer>
            Page <?= $page .' / '. $pages ?>
            <?php $page++; ?>
        </footer>
    </body>
</html>
