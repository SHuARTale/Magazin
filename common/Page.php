<?php
namespace common;
require_once "DbHelper.php";
abstract class Page
{

    private $dbh;

    public function __construct(){
        session_start();
        $this->dbh = DbHelper::getInstance("localhost", 3306, "root", "");
        if ($this->dbh->isSecure($this->getUrl())){
            if (!isset($_SESSION['login'])){
                $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
                header("Location: /auth.php");
            }
        }
    }
    public function show(): void{
        print "<html lang='ru'>";
        $this->createHeading();
        $this->createBody();
        print "</html>";
    }

    private function createHeading(){
        ?>
        <head>
            <link rel="stylesheet" type="text/css" href="/css/main.css">
            <meta charset="utf-8"/>
            <title><?php print($this->getTitle());?></title>
        </head>
        <?php
    }

    private function createBody()
    {
        print "<body style='background-size: cover' background='/img_data/b4.jpg' >";
        print "<div class='main' style='background-color: white'>";
        $this->showHeader();
        $this->showMenu();
        print "<div class='content'>";
        $this->showContent();
        print "</div>";
        $this->showFooter();
        print "</div>";
        print "</body>";
    }

    protected abstract function showContent();

    private function showHeader()
    {
        ?>
        <div class='header'>

            <div id="txtuppg">

                <img  style="width: 1000px" src="/img_data/head.jpg" >

                <div class="txtuppg"><?php
                    if ($this->getTitle()==""){
                        if(isset($_GET['prId'])){
                            $products= DbHelper::getInstance()->getProducts();
                            print $products[$_GET['prId']]['name'];
                        }
                    }

                    else print ($this->getTitle()); ?></div>

            </div>

        </div>
        <?php
    }

    protected function showMenu()
    {
        print "<div class='menu'>";
        $pages_info = $this->dbh->getPagesInfo();
        print "<table>";
        print "<tr class='mtable'>";
        foreach ($pages_info as $index => $page_info){
            $curr_page = ($page_info['url'] === $this->getUrl()) || ($page_info['alias'] === $this->getUrl());
            print "<td class='menuitem'>";
            if (!$curr_page)
                print "<a class='l_menuitem' href='{$page_info['url']}'>";
            print $page_info['name'];
            if (!$curr_page) print "</a>";
            print "</td>";
        }
        print "</tr>";
        print "</table>";
        print "</div>";
    }

    private function showFooter()
    {
        print "<div class='footer'>";
        if (isset($_SESSION['login'])){
            print "<a href='/auth.php?exit=1'>Выход</a>";
        }
        print "<div>© Этот сайт 2023</div>";
        print "</div>";
    }

    private function getTitle(): string
    {
        return $this->dbh->getTitle($this->getUrl());
    }

    private function getUrl(): string {
        return mb_split("/?/", $_SERVER['REQUEST_URI'], 1)[0];
    }
}