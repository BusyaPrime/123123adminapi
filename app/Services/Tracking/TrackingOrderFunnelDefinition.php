<?php

namespace App\Services\Tracking;

class TrackingOrderFunnelDefinition
{
    const FUNNEL_TYPE = 'order_creation';
    const SUMMARY_STEP = '__summary';

    public static function steps()
    {
        return array(
            array(
                'order' => 1,
                'name' => 'route_filled',
                'label' => 'Маршрут заполнен',
                'description' => 'Пользователь указал точки отправления и назначения.',
            ),
            array(
                'order' => 2,
                'name' => 'cargo_filled',
                'label' => 'Параметры груза',
                'description' => 'Заполнены параметры груза и перевозки.',
            ),
            array(
                'order' => 3,
                'name' => 'price_shown',
                'label' => 'Цена показана',
                'description' => 'Пользователь дошел до расчета и увидел цену.',
            ),
            array(
                'order' => 4,
                'name' => 'completed',
                'label' => 'Заказ создан',
                'description' => 'Воронка завершилась созданием заказа.',
            ),
        );
    }

    public static function availablePlatforms()
    {
        return array('all', 'android', 'ios', 'web');
    }

    public static function stepMap()
    {
        $map = array();

        foreach (static::steps() as $step) {
            $map[$step['name']] = $step;
        }

        return $map;
    }

    public static function stepNames()
    {
        return array_keys(static::stepMap());
    }

    public static function stepLabel($name)
    {
        $map = static::stepMap();

        return isset($map[$name]) ? $map[$name]['label'] : $name;
    }
}
