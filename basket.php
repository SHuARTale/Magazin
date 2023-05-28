<?php

require_once "common/Page.php";
use common\Page;
use common\DbHelper;
class basket extends Page
{

    public function __construct()
    {
        parent::__construct();
    if (isset($_POST['removeSubmit'])) {
            $res = DbHelper::getInstance()->getBasket($_SESSION['login']);
            $c = count($res);
            for ($i = 0; $i < $c; $i++) {
                if (isset($_POST['removeItemId' . "$i"])) {
                    DbHelper::getInstance()->removeFromBasket($_POST['removeItemId' . "$i"], $_SESSION['login']);
                    unset($_POST['removeItemId' . "$i"]);
                }
            }
            unset($_POST['removeSubmit']);
        }
        else if (isset($_POST['Buy'])) {
            $res = DbHelper::getInstance()->getBasket($_SESSION['login']);
            $c = count($res);
            $id=-1;

            for ($i = 0; $i < $c; $i++) {
                if (isset($_POST['removeItemId' . "$i"])) {
                    foreach ($res as $index=>$product) {
                        if ($product['id']===$_POST['removeItemId' . "$i"]){
                            $id=$index;
                        }
                    }
                    DbHelper::getInstance()->Buy($_SESSION['login'], $_POST['removeItemId' . "$i"],$res[$index]['quantity']);
                    DbHelper::getInstance()->removeFromBasket($_POST['removeItemId' . "$i"], $_SESSION['login']);
                    unset($_GET['removeItemId' . "$i"]);
                    $id=-1;
                }
            }
            unset($_POST['Buy']);
        }

    }
    protected function showContent()
    {
        $res=DbHelper::getInstance()->getBasket($_SESSION['login']);
        $allCost=0;
       print "<table>";
       print "<form method='post'>";
        foreach ($res as $index=>$product){
            ?>

            <tr>
                <td>
                   <?php print "<a href='productPage.php?prId=".$product['id']."'> <img style='width: 300px; height: 200px' src=".$product["img_source"]."></a>"; ?>

                </td>
                <td>
                    <?php print $product['name'] ?>
                </td>
                <td>
                    Количество:
                    <?php print $product['quantity'] ?>
                </td>
                <td>
                    Цена:
                    <?php print $product['price'] ?>
                    <?php $allCost+=$product['price']*$product['quantity'];?>
                </td>
                <td>

                      <?php print "<input type='checkbox' name='removeItemId". "$index'"." value=".$product['id'].">"?>

                </td>
            </tr>
        <?php
        }
        print "<tr>";
        print "Всего: ".$allCost." Рублей";
        print "</tr>";
       print "<table>";
       print "<tr>";
       print "<div style='align-content: center'>";
        print "<input type='submit' name='removeSubmit' value='Убрать'>";
        print "</div>";
        print "</tr>";
        print "<tr>";
        print "<input type='submit' name='Buy' value='Купить'>";
        print "</tr>";
    print "</form>";


    

    }
}

(new basket())->show();