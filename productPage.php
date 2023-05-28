<?php

require_once "common/Page.php";
use common\Page;
use common\DbHelper;
class productPage extends Page
{

    public function __construct()
    {
        parent::__construct();
        //print $_POST['BasketAdd'];
        if (isset($_POST['BasketAdd'])){
            DbHelper::getInstance()->addToBasket($_SESSION['login'],$_GET['prId'],$_POST['quantity']);
            unset($_POST['BasketAdd']);
        }
    }

    protected function showContent()
    {

        $products= DbHelper::getInstance()->getProducts();

        ?>
        <table>
            <tr>
                <td>
                    <a>
                        <img style="width: 300px; height: 200px" src="<?php print $products[$_GET['prId']]['img_source'] ?>">

                    </a>
                </td>
                <td>
                    <table>
                        <tr>
                            <td>
                                <?php print $products[$_GET['prId']]['name'] ?>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                Цена:
                                <?php print $products[$_GET['prId']]['price'] ?>
                            </td>

                        </tr>
                    </table>
                </td>
                <td>
                    <form method="post" >
                        <table>
                            <tr>
                            <?php
                                    if(isset($_SESSION['login'])){?>
                                <td>
                                    Количество
                                    <input type="number" name="quantity" size="10">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                  <?php
                                        print '<input type="submit" name="BasketAdd" value="Добавить в корзину" >';
                                    }
                                else{
                                        print "Войдите в аккаунт, что бы купить";
                                    }
                                ?>
                                </td>
                            </tr>

                        </table>
                    </form>

                </td>
            </tr>

        </table>
        <br>
    <?php
        print "Описание:<br> ";
        print $products[$_GET['prId']]['description'];
    }
}

(new productPage())->show();