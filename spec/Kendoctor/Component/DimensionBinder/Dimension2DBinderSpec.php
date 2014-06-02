<?php

namespace spec\Kendoctor\Component\DimensionBinder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Dimension2DBinderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kendoctor\Component\DimensionBinder\Dimension2DBinder');
        $this->shouldImplement('Kendoctor\Component\DimensionBinder\Dimension2DBinderInterface');
    }

    function it_has_4x5_dimension_by_default()
    {
        $this->getRow()->shouldBe(4);
        $this->getColumn()->shouldBe(5);
    }

    function it_finds_item_not_bound($item)
    {
        $this->findItemPosition($item)->shouldReturn(false);
    }

    function it_binds_item_at_specific_cell_and_returns_old_if_exists($item)
    {
        $this->bindItemAt($item, 2,2)->shouldReturn(null);
        $this->findItemPosition($item)->shouldReturn(array(2, 2));
    }

    function it_finds_item_at_specific_position_but_no_item_there()
    {
        $this->findItemAt(1, 1)->shouldReturn(null);
    }

    function it_succeeds_to_find_item_at($item)
    {
        $this->bindItemAt($item, 2, 2);
        $this->findItemAt(2,2)->shouldReturn($item);
    }

    function it_finds_item_at_out_of_boundary()
    {
        $this->findItemAt(5,5)->shouldReturn(null);
    }

    function it_unbinds_item_at($item) {
        $this->bindItemAt($item, 2, 2);
        $this->unbindItemAt(2,2)->shouldReturn($item);
    }

    function its_nothing_to_unbind_at()
    {
        $this->unbindItemAt(2, 2)->shouldReturn(null);
    }

    function it_fails_to_unbind_item_with_specific_one_but_not_exists($item)
    {
        $this->unbindItem($item)->shouldReturn(false);
    }

    function it_unbind_item_with_specific_one($item)
    {
        $this->bindItemAt($item, 2, 2);
        $this->unbindItem($item)->shouldReturn(array(2,2));
    }

    function it_gets_total_of_bound_items($item)
    {
        $this->getBindCount()->shouldBe(0);
        $this->bindItemAt($item, 1, 1);
        $this->getBindCount()->shouldBe(1);
    }

    function it_gets_total_bind_capacity()
    {
        $this->getBindCapacity()->shouldReturn(20);
    }

    function it_determines_binder_is_full()
    {

        $this->isFull()->shouldReturn(false);
        for($row=1; $row <= 4; $row++)
        {
            for ($column = 1; $column <=5; $column ++) {
                $this->bindItemAt(new \DateTime(), $row, $column);
            }
        }
        $this->isFull()->shouldReturn(true);
    }

    function it_auto_bind_item_at_leftopmost_position($item1,$item2)
    {
        $this->autoBind($item1)->shouldReturn(array(1,1));
        $this->autoBind($item2)->shouldReturn(array(1,2));
    }

    function it_fails_to_bind_item_at_which_position_already_has_one($item1, $item2)
    {
        $this->bindItemAt($item1, 2, 2);
        $this->shouldThrow()->duringBindItemAt($item2, 2, 2);
    }

    function it_succeeds_to_replace_item_having_swap_option_at_which_position_already_has_one($item1, $item2)
    {
        $this->bindItemAt($item1, 2, 2);
        $this->bindItemAt($item2, 2, 2, true)->shouldReturn($item1);
    }

    function it_rearranges_position_with_lefttopmost_strategy($item1, $item2)
    {
        $this->bindItemAt($item1, 2, 2);
        $this->bindItemAt($item2, 3, 3);
        $this->rearrange()->shouldReturn($this);
        $this->findItemPosition($item1)->shouldReturn(array(1, 1));
        $this->findItemPosition($item2)->shouldReturn(array(1, 2));
    }


}
