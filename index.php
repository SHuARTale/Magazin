<?php

require_once "common/Page.php";
use common\Page;



class index extends Page
{
    public function __construct()
    {
        parent::__construct();

    }

    protected function showMenu(){
        ?>
        <table>
            <tr>
                <td>
                    <?php parent::showMenu();?>
                </td>
                <td>

                </td>
                <td>
                    Поиск:
                </td>
                <td>
                    <form method="get">
                        <input name="search" type="search">
                    </form>
                </td>
            </tr>
        </table>
        <?php
    }

    protected function showContent()
    {
    //$_SESSION['login']='user1';

    print "Категории";
    ?>
            <form>
                <select name="category"  >
                    <option value="2">Торты</option>
                    <option value="3">Булки</option>
                    <option value="4">Хлеб</option>
                </select>
                <input type="submit" name="sort" value="Отсортировать">
            </form>


<?php
        if(isset($_GET['category'])){
            $result=\common\DbHelper::getInstance()->getSortProducts($_GET['category']);
            //unset($_GET['category']);
            $prodCount=count($result);
            //print $prodCount;
        }
    else if (isset($_GET['search'])){
        $result=\common\DbHelper::getInstance()->getProducts($_GET['search']);
        unset($_GET['search']);
        $prodCount=count($result);
    }
    else{
        $result=\common\DbHelper::getInstance()->getProducts();
        $prodCount= \common\DbHelper::getInstance()->getCountOfProducts();
    }


        print "<table class='mainpagetable'>";
        for ($i = 0; $i < $prodCount; $i++) {
            if ($i % 3==0) {
                print "<tr class='mtable'>";
            }
            print "<td >";
            print "<div class='mainProduct'>";


            print "<a href='productPage.php?prId=".$result[$i]['id']."'> <p style='text-align: center'>".$result[$i]['name']."<p></a>";
            print "<a href='productPage.php?prId=".$result[$i]['id']."'> <img style='width: 300px; height: 200px' src=".$result[$i]["img_source"]."></a>";
            print "<p style='text-align: center'>"."Цена: ".$result[$i]['price']."<p>";
            print "</div>";


            print "</td>";
            if ($i % 3==0 && $i!=0) {
                print "</tr>";

            }


        }
        print "</table>";
    }
}

(new index())->show();
