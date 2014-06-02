<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kendoctor
 * Email: kendoctor@163.com
 * Date: 14-6-1
 * Time: 下午7:59
 * To change this template use File | Settings | File Templates.
 */

namespace Kendoctor\Component\DimensionBinder;


Interface Dimension2dBinderInterface {
    public function getRow();
    public function getColumn();
    public function bindItemAt($item, $row, $column, $swap = false);
}