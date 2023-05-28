<?php

require_once "common/Page.php";
use common\Page;
use common\DbHelper;
class secret extends Page
{


    protected function showContent()
    {
        $userdata = DbHelper::getInstance()->getUserData($_SESSION['login']);

        print "<div> Приветствуем, ".$userdata['name']."</div>";

        ?>
        <table>
            <tr>
                <td>
                    <?php if ($userdata['img_source']!=null){?>
                        <img style="width: 200px; height: 200px" src="<?php print $userdata['img_source'] ?>">
                    <?php }
                      else{ ?>
                    <img style="width: 200px; height: 200px" src="/img_data/full_wbevBLAs.png">
                    <?php
                      }
                        ?>
                </td>
                <td>
                    <table>
                        <tr>
                            <td>
                                Имя:
                                <?php print $userdata['name'] ?>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                Логин:
                                <?php print $userdata['login'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Дата регистрации:
                                <?php print $userdata['regData'] ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <table>
                    <tr>
                        <td>
                            Заказы:
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            $orders=DbHelper::getInstance()->getOrders($_SESSION['login']);
                            foreach ($orders as $index=>$order){
                                //print "<td>";
                                print $order['name'];

                                //print $order['productId'];
                                print "<br>";
                                print "Количество: ";
                                print $order['quantity'];
                                print "<br>";
                                print "Дата заказа: ";
                                print $order['orderDate'];
                                    print "<p>";

                                //print "</td>";
                            }
                            ?>
                        </td>

                    </tr>

                </table>

            </tr>
        </table>
        <?php
    }
}

(new secret())->show();