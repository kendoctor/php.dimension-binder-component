<?php

namespace Kendoctor\Component\DimensionBinder;

class Dimension2DBinder implements Dimension2DBinderInterface
{
    private $row;
    private $column;
    private $table;
    private $bindCount = 0;

    public function __construct($row = 4, $column = 5)
    {
        $this->row = $row;
        $this->column = $column;
        $this->initTable();
    }

    private function initTable()
    {
        for($row = 1; $row <= $this->row; $row++)
        {
            $this->table[$row] = array();
            for ($column = 1; $column <= $this->column; $column++) {
                $this->table[$row][$column] = null;
            }
        }

    }

    public function getRow()
    {
        return $this->row;
    }

    public function getColumn()
    {
        return $this->column;
    }


    public function bindItemAt($item, $row, $column, $swap = false)
    {
        if ($row > $this->row || $column > $this->column) {
            throw new \Exception(sprintf(' %dX%d out of boundary in dimension %dX%d ', $row, $column, $this->row, $this->column));
        }

        $oldItem = $this->table[$row][$column];
        if($oldItem === $item) return $item;

        if(null !== $oldItem && !$swap)
        {
            throw new \Exception('This position already have one');

        }

        $this->bindCount ++;
        $this->table[$row][$column] = $item;

        return $oldItem;
    }

    public function findItemAt($row, $column)
    {
        if ($row > $this->row || $column > $this->column) {
           return null;
        }

        return $this->table[$row][$column];
    }

    public function findItemPosition($item)
    {
        foreach($this->table as $rowAt => $row) {
            foreach($row as $columnAt => $itemToCompare) {
                if($item === $itemToCompare) return array($rowAt, $columnAt);
            }
        }
        return false;
    }

    public function unbindItemAt($row, $column)
    {
        if ($row > $this->row || $column > $this->column) {
            return null;
        }

        $item = $this->table[$row][$column];
        if (null !== $item) {
            $this->bindCount--;
        }
        $this->table[$row][$column] = null;
        return $item;
    }


    public function unbindItem($item)
    {
        $coordinate = $this->findItemPosition($item);
        if(false === $coordinate) return false;
        $this->unbindItemAt($coordinate[0], $coordinate[1]);
        return $coordinate;
    }

    public function autoBind($item)
    {
        if(false !== ($coordinate = $this->findLeftToMostAvailablePosition()))
        {
            $this->bindItemAt($item, $coordinate[0],$coordinate[1]);
            return $coordinate;
        }
        return null;
    }

    private function findLeftToMostAvailablePosition()
    {
       if(!$this->isFull()){
           for($row = 1; $row <= $this->row; $row++)
           {
               for ($column = 1; $column <= $this->column; $column++) {
                   if($this->table[$row][$column] === null)
                   {
                       return array($row, $column);
                   }
               }
           }
       }
       return false;
    }

    public function getBindCount()
    {
        return $this->bindCount;
    }

    public function getBindCapacity()
    {
        return $this->row * $this->column;
    }

    public function isFull()
    {
        return $this->bindCount === $this->getBindCapacity();
    }

    public function rearrange()
    {
        $cachedAvailablePosition = array();
        $arrangedCount = 0;
        $bindCount = $this->getBindCount();

        for($row = 1; $row <= $this->row; $row++)
        {
            for ($column = 1; $column <= $this->column; $column++) {
                if($arrangedCount >= $bindCount) return $this;

                $oldItem = $this->table[$row][$column];
                if (null === $oldItem) {
                   array_push($cachedAvailablePosition,array($row,$column));
                }else
                {
                   if(count($cachedAvailablePosition) > 0)
                   {
                       $coordinate = array_shift($cachedAvailablePosition);
                       array_push($cachedAvailablePosition,array($row,$column));
                       $this->table[$row][$column] = null;
                       $this->bindItemAt($oldItem, $coordinate[0], $coordinate[1], true);
                       $arrangedCount ++;
                   }
                }
            }
        }
        return $this;
    }
}
