<?php

/*
 * Copyright (c) 2016 PIXEL FEDERATION, s.r.o.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Druid\QueryBuilder;

use Druid\Query\Aggregation\GroupBy;
use Druid\Query\Component\AggregatorInterface;
use Druid\Query\Component\DataSource\TableDataSource;
use Druid\Query\Component\DimensionSpec\DefaultDimensionSpec;
use Druid\Query\Component\FilterInterface;
use Druid\Query\Component\Granularity\PeriodGranularity;
use Druid\Query\Component\HavingInterface;
use Druid\Query\Component\Interval\Interval;
use Druid\Query\Component\PostAggregatorInterface;

/**
 * Class GroupByQueryBuilder.
 */
class GroupByQueryBuilder extends AbstractQueryBuilder
{
    protected $components = [
        'dataSource' => null,
        'dimensions' => [],
        'limitSpec' => null,
        'having' => null,
        'granularity' => null,
        'filter' => null,
        'aggregations' => [],
        'postAggregations' => [],
        'intervals' => [],
    ];

    /**
     * @param string $dataSource
     *
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        return $this->addComponent('dataSource', new TableDataSource($dataSource));
    }

    /**
     * @param string $period
     * @param string $timeZone
     *
     * @return $this
     */
    public function setGranularity($period, $timeZone = 'UTC')
    {
        return $this->addComponent('granularity', new PeriodGranularity($period, $timeZone));
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return $this
     */
    public function addInterval(\DateTime $start, \DateTime $end)
    {
        return $this->addComponent('intervals', new Interval($start, $end));
    }

    /**
     * @param AggregatorInterface $aggregator
     *
     * @return $this
     */
    public function addAggregator(AggregatorInterface $aggregator)
    {
        return $this->addComponent('aggregations', $aggregator);
    }

    /**
     * @param string $dimension
     * @param string $outputName
     *
     * @return $this
     */
    public function addDimension($dimension, $outputName)
    {
        return $this->addComponent('dimensions', new DefaultDimensionSpec($dimension, $outputName));
    }

    /**
     * @param PostAggregatorInterface $postAggregator
     *
     * @return $this
     */
    public function addPostAggregator(PostAggregatorInterface $postAggregator)
    {
        return $this->addComponent('postAggregations', $postAggregator);
    }

    /**
     * @param FilterInterface $filter
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter)
    {
        return $this->addComponent('filter', $filter);
    }

    /**
     * @param HavingInterface $having
     *
     * @return $this
     */
    public function setHaving(HavingInterface $having)
    {
        return $this->addComponent('having', $having);
    }

    /**
     * @return GroupBy
     */
    public function getQuery()
    {
        $query = new GroupBy();
        foreach ($this->components as $componentName => $component) {
            if (!empty($component)) {
                $method = 'set'.ucfirst($componentName);
                $query->$method($component);
            }
        }

        return $query;
    }
}
