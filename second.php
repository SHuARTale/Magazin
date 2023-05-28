<?php

require_once "common/Page.php";
use common\Page;

class graph
{
    private $vtx=array();
    public function __construct(string $fileName){
        $graphData =file($fileName);
        foreach ($graphData as $num=>$value){
            $this->vtx[$num]=new vertex($num+1,explode( ' ',$value));
        }
    }

    public function findPath($start,$finish){
        $this->vtx[$start-1]->setPathLength(0);
        while(($v = $this->findMinVertex()) !== null){
            $edges = $v->getEdges();
            foreach ($edges as $i=>$edge){
                if($edge > 0){
                    $ln = $v->getPathLength() + $edge;
                    if($ln < $this->vtx[$i]->getPathLength()) {
                        $this->vtx[$i]->setPathLength($ln);
                        $this->vtx[$i]->setPrevVertex($v->getNumber() - 1);
                    }
                }
            }
            $v->setIsVisited(true);
        }

        $res = array();
        $curr = $finish - 1;
        while($curr !== $start - 1){
            $res[] = $this->vtx[$curr];
            if ($this->vtx[$curr]->getPrevVertex() >= 0) {
                $curr = $this->vtx[$curr]->getPrevVertex();
            } else break;
        }
        $res[] = $this->vtx[$start - 1];
        return array_reverse($res);
    }
    private function findMinVertex(): ?vertex{
        $min=null;
        foreach ($this->vtx as $i=>$v){
            if(($min === null || $min->getPathLength() > $v->getPathLength()) && !$v->isVisited())
                $min = $v;
        }
        return $min;
    }

}

class vertex
{

    private int $number;
    private array $edges=array();
    private bool $isVisited = false;
    private int $pathLength=PHP_INT_MAX;
    private int $prevVertex=-1;

    public function __construct($num,$edges){
        $this->number=$num;
        foreach ($edges as $i=>$e){
            $this->edges[$i]=(int)$e;
        }
    }

    /**
     * @return bool
     */
    public function isVisited(): bool
    {
        return $this->isVisited;
    }

    /**
     * @param bool $isVisited
     */
    public function setIsVisited(bool $isVisited): void
    {
        $this->isVisited = $isVisited;
    }

    /**
     * @return int
     */
    public function getPathLength(): int
    {
        return $this->pathLength;
    }

    /**
     * @param int $pathLength
     */
    public function setPathLength(int $pathLength): void
    {
        $this->pathLength = $pathLength;
    }

    /**
     * @return int
     */
    public function getPrevVertex(): int
    {
        return $this->prevVertex;
    }

    /**
     * @param int $prevVertex
     */
    public function setPrevVertex(int $prevVertex): void
    {
        $this->prevVertex = $prevVertex;
    }

    /**
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}

class second extends Page
{

    protected function showContent()
    {
        $g = new graph("data/graph.txt");
        $path = $g->findPath(3, 2);
        foreach ($path as $v){
            print $v->getNumber()." ";
        }
        print("Длина пути: ".$path[sizeof($path)-1]->getPathLength());
    }
}

(new second())->show();