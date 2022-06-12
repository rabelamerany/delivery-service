<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Facture</title>
        <link rel="stylesheet" href="assets/css/paper.min.css"/>
        <link rel="stylesheet" href="assets/css/print.css"/>
        <style>
            footer > span {
                padding-right: 20px;
            }
        </style>
    </head>
    <body>
        <?php 
        $first_page_count = 13;
        $consecutive_pages_count = 19;
        $page = 1; 
        $num_orders = count($orders);
        $pages = $num_orders > $consecutive_pages_count ? 1+ceil(($num_orders-$first_page_count)/ $consecutive_pages_count) : 1; 
        ?>
        <header>
            <div id="company">
                <div class="logo">
                    <img src="assets/images/logo.png">
                    <span style="font-size: 20px;"><?= $app_name ?></span>
                </div>
                <div><?= $app_name ?></div>
                <div><?= $app_address1 ?></div>
                <div><?= $app_address2 ?></div>
                <div><?= $app_city . ' ' . $app_postalCode ?></div>
                <div><?= $app_phone ?></div>
            </div>
            <div id="customer">
                <div style="padding-left: 10px;border-left: 1px solid black;">
                    <div>Facture</div>
                    <div>N°<?= (new DateTime())->format("Y").str_pad($app_InvoiceCounter, 3, "0", STR_PAD_LEFT) ?></div>
                    <div>Généré le <?= (new DateTime())->format('d/m/Y') ?></div>
                    <div>Entre le <?= (new DateTime($delivery_from))->format('d/m/Y') ?> et <?= (new DateTime($delivery_to))->format('d/m/Y') ?></div>
                </div>
                <br><br>
                <div style="padding: 10px;border: 1px solid black;">
                    <div><strong>Client</strong></div>
                    <div><?= $customer_name ?></div>
                    <div><?= $customer_address1 ?></div>
                    <div><?= $customer_address2 ?></div>
                    <div><?= $customer_city . ' ' . $customer_postalCode ?></div>
                    <div><?= $customer_phone ?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <br><br><br><br><br><br>
            <!-- <h1>FACTURE</h1> -->
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th width="5%">N° Comm</th>
                        <th width="7%">Date et heure</th>
                        <th width="50%">Désignation</th>
                        <th width="15%">Coût</th>
                    </tr>
                </thead>
                <tbody>
                <?php $tht = 0; ?>
                <?php for ($i= 1, $count=1; $i <= $num_orders; $i++, $count++) { ?>
                    <?php //$tht += ($orders[$i-1]['items_cost']+$orders[$i-1]['delivery_cost']); ?>
                    <tr>
                        <td style="text-align: center"><?= $orders[$i-1]['id'] ?></td>
                        <td style="text-align: center"><?= $orders[$i-1]['date_delivered'] ?></td>
                        <td style="text-align: left"><?= "Livraison à ".$orders[$i-1]['for'].", $customer_address1, $customer_address2, $customer_city, $customer_postalCode" ?></td>
                        <td style="text-align: center"><?= $orders[$i-1]['delivery_cost'] ?> DH</td>
                        <?php $tht += $orders[$i-1]['delivery_cost']; ?>
                    </tr>
                <?php 
                if (($count == $first_page_count && 
                    $i <=  $consecutive_pages_count) || ($count%($consecutive_pages_count+1) == 0 &&
                    $i < $num_orders)) { 
                ?>
                </tbody>
            </table>
            <footer>
                <?= $app_name.' '.$app_address1 .' '. $app_address2.' '.$app_postalCode.' '.$app_city.' Tél: '.$app_phone ?>
                <br>
                <strong>RC: </strong><span><?= $app_rc ?></span><strong>Patente: </strong><span><?= $app_patente ?></span><strong>N.IF: </strong><span><?= $app_if ?></span><strong>ICE: </strong><span><?= $app_ice ?></span><strong>R.I.B: </strong><span><?= $app_rib ?></span>
                <div class="page">
                      Page <?= $page .' / '. $pages ?>
                       <?php $count = 1; $page++; ?>
                </div>
            </footer>
            <div style="page-break-inside:avoid; page-break-after:always;"></div>
            <table>
                <tbody>
                <?php }
                } ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;text-align: left;">
                        <td colspan="3">Total H.T.:</td>
                        <td><?= $tht ?> DH</td>
                    </tr>
                    <tr style="font-weight: bold;text-align: left;">
                        <td colspan="3">TVA 20%:</td>
                        <td><?= $tht*0.2 ?> DH</td>
                    </tr>
                    <tr style="font-weight: bold;text-align: left;">
                        <td colspan="3">Total T.T.C.:</td>
                        <td><?= $tht*1.2 ?> DH</td>
                    </tr>
                </tfoot>
            </table>
        </main>
        <footer>
            <?= $app_name.' '.$app_address1 .' '. $app_address2.' '.$app_postalCode.' '.$app_city.' Tél: '.$app_phone ?>
            <br>
            <strong>RC: </strong><span><?= $app_rc ?></span><strong>Patente: </strong><span><?= $app_patente ?></span><strong>N.IF: </strong><span><?= $app_if ?></span><strong>ICE: </strong><span><?= $app_ice ?></span><strong>R.I.B: </strong><span><?= $app_rib ?></span>
            <div class="page">
                    Page <?= $page .' / '. $pages ?>
                    <?php $count = 1; $page++; ?>
            </div>
        </footer>
    </body>
</html>
