@extends('admin.layout')

@php
    $dashboard = $dashboard ?? [];
    $filters = $dashboard['filters'] ?? [];
    $options = $dashboard['options'] ?? ['years' => [], 'months' => [], 'periods' => []];
    $range = $dashboard['range'] ?? [];
    $kpis = $dashboard['kpis'] ?? [];
    $charts = $dashboard['charts'] ?? ['main' => [], 'secondary' => [], 'payments' => [], 'origins' => []];
    $routes = $dashboard['routes'] ?? [];
    $clients = $dashboard['clients'] ?? [];
    $hasData = !empty($dashboard['has_data']);
    $currentTotals = $dashboard['totals']['current'] ?? [];
    $paymentSummary = $charts['payments']['summary'] ?? [];
    $originSummary = $charts['origins']['summary'] ?? [];
    $paymentAccentCycle = ['primary', 'compare', 'teal', 'purple', 'green'];
    $selectedPeriodLabel = $options['periods'][$filters['period'] ?? 'month'] ?? 'Месяц';
    $compareEnabled = !empty($filters['compare']);
    $compareLabel = $compareEnabled ? ($range['previous_label'] ?? 'Нет данных') : 'Сравнение отключено';
    $clientGroup = $filters['client_group'] ?? 'company';
    $clientGroupOptions = $options['client_groups'] ?? ['company' => 'По компаниям', 'employee' => 'По сотрудникам'];
    $isYearPeriod = ($filters['period'] ?? 'month') === 'year';
    $clientTitleNote = $clientGroup === 'company'
        ? 'Топ ' . count($clients) . ' компаний по объему завершенных заказов'
        : 'Топ ' . count($clients) . ' клиентов по объему завершенных заказов';
    $clientNameLabel = $clientGroup === 'company' ? 'Сотрудники' : 'Клиент';
    $formatRank = function ($index) {
        return str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
    };

    $formatNumber = function ($value) {
        return number_format((float) $value, 0, '.', ' ');
    };
    $formatMoney = function ($value) use ($formatNumber) {
        return $formatNumber($value);
    };
    $orderWord = function ($value) {
        $value = abs((int) $value);
        $mod100 = $value % 100;
        $mod10 = $value % 10;
        if ($mod100 >= 11 && $mod100 <= 14) {
            return 'заказов';
        }
        if ($mod10 === 1) {
            return 'заказ';
        }
        if ($mod10 >= 2 && $mod10 <= 4) {
            return 'заказа';
        }
        return 'заказов';
    };
    $formatOrders = function ($value) use ($formatNumber, $orderWord) {
        return $formatNumber($value) . ' ' . $orderWord($value);
    };
    $formatPercent = function ($value) {
        return number_format((float) $value, 2, '.', ' ') . '%';
    };
    $formatValue = function ($value, $type) use ($formatMoney, $formatNumber, $formatPercent) {
        if ($type === 'money') {
            return $formatMoney($value);
        }
        if ($type === 'percent') {
            return $formatPercent($value);
        }
        return $formatNumber($value);
    };
    $normalizeDeltaForDisplay = function ($kpi, $value) {
        if ($value === null) {
            return null;
        }

        if (($kpi['label'] ?? '') === 'Доля отмен') {
            return 0 - (float) $value;
        }

        return (float) $value;
    };
    $formatDelta = function ($kpiOrValue, $valueOrType, $type = null) use ($normalizeDeltaForDisplay) {
        $kpi = is_array($kpiOrValue) ? $kpiOrValue : [];
        $value = is_array($kpiOrValue) ? $valueOrType : $kpiOrValue;
        $deltaType = is_array($kpiOrValue) ? $type : $valueOrType;

        if ($value === null) {
            return '-';
        }

        $displayValue = $normalizeDeltaForDisplay($kpi, $value);

        if ($deltaType === 'pp') {
            return (($displayValue > 0) ? '+' : '') . number_format((float) $displayValue, 2, '.', ' ') . ' п.п.';
        }
        return (($displayValue > 0) ? '+' : '') . number_format((float) $displayValue, 2, '.', ' ') . '%';
    };
    $deltaClass = function ($value) {
        if ($value === null || (float) $value === 0.0) {
            return 'is-neutral';
        }
        return ((float) $value > 0) ? 'is-up' : 'is-down';
    };
    $inverseDeltaClass = function ($value) {
        if ($value === null || (float) $value === 0.0) {
            return 'is-neutral';
        }
        return ((float) $value > 0) ? 'is-down' : 'is-up';
    };
    $valueToneClass = function ($kpi) {
        $currentValue = (float) ($kpi['value'] ?? 0);
        $compareValue = (float) ($kpi['compare_value'] ?? 0);
        $difference = $currentValue - $compareValue;

        if (abs($difference) < 0.00001) {
            return 'tone-default';
        }

        $isInverse = ($kpi['label'] ?? '') === 'Доля отмен';

        if ($difference > 0) {
            return $isInverse ? 'tone-down' : 'tone-up';
        }

        return $isInverse ? 'tone-up' : 'tone-down';
    };
    $formatCompareHint = function ($kpi) use ($formatValue) {
        $currentValue = $kpi['value'] ?? 0;
        $compareValue = $kpi['compare_value'] ?? 0;
        $delta = $kpi['delta'] ?? null;

        if ($delta === null) {
            return 'В предыдущем периоде нет базы для сравнения';
        }

        if ((float) $compareValue === 0.0) {
            return (float) $currentValue === 0.0
                ? 'Оба периода без значения'
                : 'Рост с нулевой базы';
        }

        return 'Предыдущий период: ' . $formatValue($compareValue, $kpi['compare_type']);
    };
    $formatDifferenceHint = function ($kpi) use ($formatNumber, $normalizeDeltaForDisplay) {
        $currentValue = (float) ($kpi['value'] ?? 0);
        $compareValue = (float) ($kpi['compare_value'] ?? 0);
        $difference = $normalizeDeltaForDisplay($kpi, $currentValue - $compareValue);
        $sign = $difference > 0 ? '+' : '';

        if (($kpi['delta_type'] ?? null) === 'pp' || ($kpi['compare_type'] ?? null) === 'percent') {
            return 'Разница: ' . $sign . number_format($difference, 2, '.', ' ') . ' п.п.';
        }

        return 'Разница: ' . $sign . $formatNumber($difference);
    };
@endphp

@section('center_content')
        <div class="gmv-dashboard">
            <form id="dashboard-filters" class="gmv-topbar" action="{{ route('admin.dashboards.index') }}" method="GET">
                <div class="gmv-topbar__main">
                    <div class="gmv-topbar__meta">
                        <div class="gmv-pill">GMV</div>
                        <div class="gmv-headline">
                            <div class="gmv-headline__title">{{ $range['anchor_label'] ?? 'GMV' }}</div>
                            <div class="gmv-headline__sub">
                                <span>{{ $range['current_label'] ?? 'Нет данных' }}</span>
                                <span>{{ $compareEnabled ? ('Сравнение: ' . $compareLabel) : ('Обновлено ' . ($dashboard['generated_at'] ?? '-')) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="gmv-topbar__controls">
                        <div class="gmv-period-picker">
                            <span class="gmv-period-picker__title">Период</span>
                            <div class="gmv-period-picker__fields">
                                <label class="gmv-select gmv-select--compact">
                                    <span>Год</span>
                                    <select name="year">
                                        @foreach($options['years'] as $yearOption)
                                            <option value="{{ $yearOption }}" {{ (int) ($filters['year'] ?? 0) === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                @unless($isYearPeriod)
                                    <label class="gmv-select gmv-select--compact">
                                        <span>Месяц</span>
                                        <select name="month">
                                            @foreach($options['months'] as $monthOption)
                                                <option value="{{ $monthOption['value'] }}" {{ (int) ($filters['month'] ?? 0) === (int) $monthOption['value'] ? 'selected' : '' }}>{{ $monthOption['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                @endunless
                            </div>
                        </div>
                        @if($compareEnabled)
                            <div class="gmv-period-picker">
                                <span class="gmv-period-picker__title">Сравнить с</span>
                                <div class="gmv-period-picker__fields">
                                    <label class="gmv-select gmv-select--compact">
                                        <span>Год</span>
                                        <select name="compare_year">
                                            @foreach($options['years'] as $yearOption)
                                                <option value="{{ $yearOption }}" {{ (int) ($filters['compare_year'] ?? 0) === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    @unless($isYearPeriod)
                                        <label class="gmv-select gmv-select--compact">
                                            <span>Месяц</span>
                                            <select name="compare_month">
                                                @foreach($options['months'] as $monthOption)
                                                    <option value="{{ $monthOption['value'] }}" {{ (int) ($filters['compare_month'] ?? 0) === (int) $monthOption['value'] ? 'selected' : '' }}>{{ $monthOption['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endunless
                                </div>
                            </div>
                        @endif
                        <div class="gmv-period-picker gmv-period-picker--single gmv-period-picker--mode">
                            <span class="gmv-period-picker__title">Режим</span>
                            <div class="gmv-period-group" style="--gmv-period-count: {{ max(count($options['periods'] ?? []), 1) }};">
                                @foreach($options['periods'] as $periodValue => $periodLabel)
                                    <button type="button" data-period="{{ $periodValue }}" class="gmv-period-btn {{ ($filters['period'] ?? '') === $periodValue ? 'is-active' : '' }}">
                                        {{ $periodLabel }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="period" value="{{ $filters['period'] ?? 'month' }}" id="dashboard-period-input">
                        <input type="hidden" name="client_group" value="{{ $filters['client_group'] ?? 'company' }}">
                        <div class="gmv-topbar__actions">
                            <div class="gmv-topbar__toggle">
                                <input type="hidden" name="compare" value="0">
                                <label class="gmv-compare-toggle gmv-compare-toggle--action">
                                    <input type="checkbox" name="compare" value="1" {{ $compareEnabled ? 'checked' : '' }}>
                                    <span class="gmv-compare-toggle__track">
                                        <span class="gmv-compare-toggle__thumb"></span>
                                    </span>
                                    <span class="gmv-compare-toggle__label">Сравнить</span>
                                </label>
                            </div>
                            <button type="submit" class="gmv-apply">Применить</button>
                        </div>
                    </div>
                </div>
                <div class="gmv-topbar__insights">
                    <div class="gmv-insight">
                        <span>Период</span>
                        <strong>{{ $selectedPeriodLabel }}</strong>
                    </div>
                    <div class="gmv-insight">
                        <span>Источник GMV</span>
                        <strong>Завершенные заказы</strong>
                    </div>
                    <div class="gmv-insight">
                        <span>Текущий GMV</span>
                        <strong>{{ $formatMoney($currentTotals['gmv'] ?? 0) }}</strong>
                    </div>
                </div>
            </form>

            <div class="gmv-kpi-row">
                @foreach($kpis as $kpi)
                    @php
                        $kpiDeltaClass = ($kpi['label'] ?? '') === 'Доля отмен'
                            ? $inverseDeltaClass($kpi['delta'] ?? null)
                            : $deltaClass($kpi['delta'] ?? null);
                        $kpiToneClass = $valueToneClass($kpi);
                    @endphp
                    <section class="gmv-kpi gmv-kpi--{{ $kpi['accent'] }}">
                        <div class="gmv-kpi__label">{{ $kpi['label'] }}</div>
                        <div class="gmv-kpi__value {{ $kpiToneClass }}">{{ $formatValue($kpi['value'], $kpi['value_type']) }}</div>
                        <div class="gmv-kpi__compare">
                            @if($compareEnabled)
                                <span class="gmv-kpi__delta {{ $kpiDeltaClass }}">{{ $formatDelta($kpi, $kpi['delta'], $kpi['delta_type']) }}</span>
                                <div class="gmv-kpi__compare-copy">
                                    <span>{{ $formatCompareHint($kpi) }}</span>
                                    <span class="gmv-kpi__difference {{ $kpiToneClass }}">{{ $formatDifferenceHint($kpi) }}</span>
                                </div>
                            @else
                                <span>{{ $range['current_label'] ?? 'Сравнение отключено' }}</span>
                            @endif
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="gmv-grid gmv-grid--single">
                <section class="gmv-card">
                    <div class="gmv-card__header">
                        <div>
                            <div class="gmv-card__eyebrow">Основной график</div>
                            <h3 class="gmv-card__title">{{ $range['main_title'] ?? 'GMV' }}</h3>
                            <div class="gmv-card__note">{{ $formatOrders($currentTotals['done_orders'] ?? 0) }} · {{ $formatMoney($currentTotals['gmv'] ?? 0) }}</div>
                        </div>
                        <div class="gmv-legend">
                            <span><i class="is-current"></i>{{ $range['current_label'] ?? 'Текущий период' }}</span>
                            @if($compareEnabled)
                                <span><i class="is-previous"></i>{{ $range['previous_label'] ?? 'Предыдущий период' }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="gmv-card__canvas">
                        <canvas id="dashboard-main-chart" height="110"></canvas>
                        @unless($hasData)
                            <div class="gmv-empty">Нет завершенных заказов за выбранный период.</div>
                        @endunless
                    </div>
                </section>
            </div>

            <div class="gmv-grid gmv-grid--split">
                <section class="gmv-card">
                    <div class="gmv-card__header">
                        <div>
                            <div class="gmv-card__eyebrow">Разбивка</div>
                            <h3 class="gmv-card__title">GMV по способам оплаты</h3>
                            <div class="gmv-card__note">{{ count($paymentSummary) }} способов оплаты в структуре периода</div>
                        </div>
                    </div>
                    @php
                        $paymentTotalGmv = 0;
                        $paymentTotalOrders = 0;
                        foreach ($paymentSummary as $paymentItem) {
                            $paymentTotalGmv += (int) $paymentItem['gmv'];
                            $paymentTotalOrders += (int) $paymentItem['orders'];
                        }
                    @endphp
                    <div class="gmv-payment-layout">
                        <div class="gmv-payment-hero">
                            <div class="gmv-card__canvas gmv-card__canvas--small gmv-card__canvas--donut">
                                <canvas id="dashboard-payment-chart" height="180"></canvas>
                                @if(count($paymentSummary))
                                    <div class="gmv-donut-center">
                                        <span>Всего GMV</span>
                                        <strong>{{ $formatMoney($paymentTotalGmv) }}</strong>
                                        <small>{{ $formatOrders($paymentTotalOrders) }}</small>
                                    </div>
                                @else
                                    <div class="gmv-empty">Нет данных по оплатам.</div>
                                @endif
                            </div>
                            <div class="gmv-payment-hero__foot">
                                Структура завершенных заказов по способам оплаты за выбранный период.
                            </div>
                        </div>
                        <div class="gmv-payment-stack">
                            @forelse($paymentSummary as $index => $payment)
                                @php
                                    $accent = $paymentAccentCycle[$index % count($paymentAccentCycle)];
                                    $barWidth = max(8, min(100, (float) $payment['share']));
                                @endphp
                                <div class="gmv-payment-card gmv-payment-card--{{ $accent }}">
                                    <div class="gmv-payment-card__head">
                                        <div class="gmv-payment-card__label">
                                            <span class="gmv-payment-card__dot"></span>
                                            <strong>{{ $payment['label'] }}</strong>
                                        </div>
                                        <span class="gmv-soft-badge">{{ $formatPercent($payment['share']) }}</span>
                                    </div>
                                    <div class="gmv-payment-card__value">{{ $formatMoney($payment['gmv']) }}</div>
                                    <div class="gmv-payment-card__meta">
                                        <span>{{ $formatOrders($payment['orders']) }}</span>
                                        <span>Доля GMV</span>
                                    </div>
                                    <div class="gmv-payment-card__bar">
                                        <span style="width: {{ $barWidth }}%;"></span>
                                    </div>
                                </div>
                            @empty
                                <div class="gmv-empty gmv-empty--inline">Нет данных по оплатам.</div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section class="gmv-card">
                    <div class="gmv-card__header">
                        <div>
                            <div class="gmv-card__eyebrow">География</div>
                            <h3 class="gmv-card__title">Точки отправления</h3>
                            <div class="gmv-card__note">Топ {{ count($originSummary) }} регионов по GMV</div>
                        </div>
                    </div>
                    <div class="gmv-card__canvas gmv-card__canvas--medium">
                        <canvas id="dashboard-origin-chart" height="220"></canvas>
                        @unless(count($originSummary))
                            <div class="gmv-empty">Нет географических данных.</div>
                        @endunless
                    </div>
                    <div class="gmv-origin-list">
                        @forelse($originSummary as $index => $origin)
                            <div class="gmv-origin-item">
                                <div class="gmv-origin-item__head">
                                    <span class="gmv-rank">{{ $formatRank($index) }}</span>
                                    <div class="gmv-origin-item__copy">
                                        <strong>{{ $origin['label'] }}</strong>
                                        <span class="gmv-soft-badge">{{ $formatOrders($origin['orders']) }}</span>
                                    </div>
                                    <strong class="gmv-origin-item__value">{{ $formatMoney($origin['gmv']) }}</strong>
                                </div>
                                <div class="gmv-origin-item__bar">
                                    <span style="width: {{ $origin['width'] }}%;"></span>
                                </div>
                            </div>
                        @empty
                            <div class="gmv-empty gmv-empty--inline">Нет географических данных.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="gmv-grid gmv-grid--split">
                <section class="gmv-card">
                    <div class="gmv-card__header">
                        <div>
                            <div class="gmv-card__eyebrow">Вторичный срез</div>
                            <h3 class="gmv-card__title">{{ $range['secondary_title'] ?? 'GMV внутри периода' }}</h3>
                            <div class="gmv-card__note">Дополнительная группировка внутри выбранного интервала</div>
                        </div>
                        <div class="gmv-legend">
                            <span><i class="is-current"></i>Текущий</span>
                            @if($compareEnabled)
                                <span><i class="is-previous"></i>Предыдущий</span>
                            @endif
                        </div>
                    </div>
                    <div class="gmv-card__canvas">
                        <canvas id="dashboard-secondary-chart" height="180"></canvas>
                        @unless($hasData)
                            <div class="gmv-empty">Нет данных для дополнительного среза.</div>
                        @endunless
                    </div>
                </section>

                <section class="gmv-card">
                    <div class="gmv-card__header">
                        <div>
                            <div class="gmv-card__eyebrow">Направления</div>
                            <h3 class="gmv-card__title">Топ маршрутов по GMV</h3>
                            <div class="gmv-card__note">{{ count($routes) }} маршрутов в лидерах периода</div>
                        </div>
                    </div>
                    <div class="gmv-route-list">
                        @forelse($routes as $index => $route)
                            <div class="gmv-route-item">
                                <div class="gmv-route-item__head">
                                    <span class="gmv-rank">{{ $formatRank($index) }}</span>
                                    <div class="gmv-route-item__label">
                                        <strong>{{ $route['label'] }}</strong>
                                        <span class="gmv-soft-badge">{{ $formatOrders($route['orders']) }}</span>
                                    </div>
                                    <strong class="gmv-route-item__amount">{{ $formatMoney($route['gmv']) }}</strong>
                                </div>
                                <div class="gmv-route-item__bar">
                                    <span style="width: {{ $route['width'] }}%;"></span>
                                </div>
                                <div class="gmv-route-item__meta">
                                    <small>Доля в периоде {{ $formatPercent($route['share']) }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="gmv-empty gmv-empty--inline">Нет данных по направлениям.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="gmv-card">
                <div class="gmv-card__header">
                    <div>
                        <div class="gmv-card__eyebrow">Клиенты</div>
                        <h3 class="gmv-card__title">Разбивка GMV по клиентам / компаниям</h3>
                        <div class="gmv-card__note">{{ $clientTitleNote }}</div>
                    </div>
                    <form class="gmv-card__toolbar" action="{{ route('admin.dashboards.index') }}" method="GET">
                        <input type="hidden" name="year" value="{{ $filters['year'] ?? '' }}">
                        @unless($isYearPeriod)
                            <input type="hidden" name="month" value="{{ $filters['month'] ?? '' }}">
                        @endunless
                        <input type="hidden" name="period" value="{{ $filters['period'] ?? 'month' }}">
                        <input type="hidden" name="compare" value="{{ $compareEnabled ? 1 : 0 }}">
                        <input type="hidden" name="compare_year" value="{{ $filters['compare_year'] ?? '' }}">
                        @unless($isYearPeriod)
                            <input type="hidden" name="compare_month" value="{{ $filters['compare_month'] ?? '' }}">
                        @endunless
                        <label class="gmv-select gmv-select--compact gmv-select--toolbar">
                            <span>Группировка</span>
                            <select name="client_group" class="js-client-group-select">
                                @foreach($clientGroupOptions as $groupValue => $groupLabel)
                                    <option value="{{ $groupValue }}" {{ ($filters['client_group'] ?? 'company') === $groupValue ? 'selected' : '' }}>{{ $groupLabel }}</option>
                                @endforeach
                            </select>
                        </label>
                    </form>
                </div>
                @if(count($clients))
                    <div class="gmv-table-wrap">
                        <table class="gmv-table">
                            <thead>
                            <tr>
                                <th>{{ $clientNameLabel }}</th>
                                <th>Компания</th>
                                <th>Заказы</th>
                                <th>GMV</th>
                                <th>Ср. чек</th>
                                <th>Доля</th>
                                @if($compareEnabled)
                                    <th>GMV предыдущего периода</th>
                                    <th>Изм.</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td><div class="gmv-client-name">{{ $client['name'] }}</div></td>
                                    <td><span class="gmv-company-pill">{{ $client['company'] }}</span></td>
                                    <td>{{ $formatNumber($client['orders']) }}</td>
                                    <td>{{ $formatMoney($client['gmv']) }}</td>
                                    <td>{{ $formatMoney($client['average_check']) }}</td>
                                    <td>{{ $formatPercent($client['share']) }}</td>
                                    @if($compareEnabled)
                                        <td>{{ $formatMoney($client['previous_gmv']) }}</td>
                                        <td><span class="gmv-delta {{ $deltaClass($client['delta']) }}">{{ $formatDelta($client['delta'], 'percent') }}</span></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="gmv-empty gmv-empty--block">Нет клиентских данных для выбранного периода.</div>
                @endif
            </section>
        </div>

        <script id="dashboard-payload" type="application/json">{!! json_encode($dashboard, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('styles')
    <style>
        :root {
            --casva-blue: #1d6ea5;
            --casva-blue-deep: #10304a;
            --casva-blue-soft: #4ca1d1;
            --casva-gold: #f0b24f;
            --casva-teal: #229a96;
            --casva-purple: #6a78d1;
            --casva-green: #2db37a;
            --casva-surface: #ffffff;
            --casva-border: #d7e4ef;
            --casva-text: #17324b;
            --casva-muted: #6a7f97;
        }

        .gmv-dashboard {
            background:
                radial-gradient(circle at top right, rgba(29, 110, 165, 0.16), transparent 34%),
                radial-gradient(circle at left center, rgba(240, 178, 79, 0.12), transparent 26%),
                linear-gradient(180deg, #fbfdff 0%, #eef5fb 100%);
            color: var(--casva-text);
            font-family: inherit;
            padding: 16px;
            box-shadow: 0 28px 64px rgba(12, 58, 89, 0.09);
            position: relative;
        }

        .gmv-dashboard:before,
        .gmv-dashboard:after {
            content: '';
            position: absolute;
            pointer-events: none;
            border-radius: 50%;
            filter: blur(6px);
            opacity: 0.65;
            z-index: 0;
        }

        .gmv-dashboard > * {
            position: relative;
            z-index: 1;
        }

        .gmv-dashboard:before {
            width: 260px;
            height: 260px;
            top: 18px;
            right: 24px;
            background: radial-gradient(circle, rgba(29, 110, 165, 0.15), transparent 72%);
        }

        .gmv-dashboard:after {
            width: 220px;
            height: 220px;
            left: -38px;
            bottom: 36px;
            background: radial-gradient(circle, rgba(240, 178, 79, 0.16), transparent 70%);
        }

        .gmv-topbar,
        .gmv-topbar__main,
        .gmv-topbar__meta,
        .gmv-topbar__controls,
        .gmv-headline__sub,
        .gmv-legend,
        .gmv-card__header,
        .gmv-kpi__compare,
        .gmv-payment-card__head,
        .gmv-payment-card__meta {
            display: flex;
        }

        .gmv-topbar,
        .gmv-card__header,
        .gmv-payment-card__head,
        .gmv-payment-card__meta {
            justify-content: space-between;
        }

        .gmv-topbar,
        .gmv-topbar__meta,
        .gmv-topbar__controls,
        .gmv-payment-card__head,
        .gmv-payment-card__meta {
            align-items: center;
        }

        .gmv-topbar {
            gap: 14px;
            flex-direction: column;
            align-items: stretch;
            background:
                linear-gradient(135deg, rgba(16, 48, 74, 0.02), rgba(29, 110, 165, 0.035)),
                rgba(255, 255, 255, 0.96);
            border: 1px solid #d9e5ef;
            border-radius: 24px;
            padding: 14px 16px;
            margin-bottom: 12px;
            box-shadow: 0 18px 36px rgba(12, 58, 89, 0.07);
            position: relative;
            overflow: hidden;
        }

        .gmv-topbar > * {
            position: relative;
            z-index: 1;
        }

        .gmv-topbar:before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, #1d6ea5, #f0b24f 48%, #229a96);
            opacity: 0.9;
        }

        .gmv-topbar__main {
            gap: 16px;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0;
        }

        .gmv-topbar__meta {
            gap: 20px;
            min-width: 0;
        }

        .gmv-topbar__controls {
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: stretch;
        }

        .gmv-topbar__insights {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .gmv-insight {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(243, 248, 252, 0.94));
            border: 1px solid #dce8f1;
            border-radius: 18px;
            padding: 15px 16px 16px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.92),
                0 10px 20px rgba(12, 58, 89, 0.04);
            position: relative;
            overflow: hidden;
        }

        .gmv-insight:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--casva-blue), var(--casva-gold));
            opacity: 0.9;
        }

        .gmv-insight span {
            display: block;
            color: var(--casva-muted);
            font-size: 10px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .gmv-insight strong {
            display: block;
            margin-top: 6px;
            color: var(--casva-blue-deep);
            font-size: 15px;
            line-height: 1.45;
            font-weight: 800;
        }

        .gmv-pill,
        .gmv-card__eyebrow,
        .gmv-select span,
        .gmv-kpi__label,
        .gmv-donut-center span {
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--casva-muted);
        }

        .gmv-pill {
            background: linear-gradient(135deg, rgba(29, 110, 165, 0.1), rgba(12, 58, 89, 0.15));
            border: 1px solid rgba(29, 110, 165, 0.2);
            border-radius: 999px;
            color: var(--casva-blue);
            padding: 10px 16px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.82);
        }

        .gmv-headline__title {
            font-size: clamp(30px, 3.2vw, 38px);
            font-weight: 800;
            line-height: 1.05;
            color: var(--casva-blue-deep);
            letter-spacing: -0.03em;
        }

        .gmv-headline__sub {
            gap: 10px;
            flex-wrap: wrap;
            color: var(--casva-muted);
            font-size: 12px;
            margin-top: 8px;
        }

        .gmv-headline__sub span {
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(215, 228, 239, 0.9);
            border-radius: 999px;
            padding: 7px 10px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88);
        }

        .gmv-select {
            display: flex;
            flex-direction: column;
            gap: 6px;
            height: 100%;
        }

        .gmv-select select {
            appearance: none;
            background:
                linear-gradient(180deg, #ffffff, #f5faff);
            border: 1px solid #c7d8e6;
            border-radius: 14px;
            color: var(--casva-text);
            min-width: 148px;
            min-height: 48px;
            padding: 12px 14px;
            box-shadow:
                0 8px 18px rgba(12, 58, 89, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.92);
        }

        .gmv-select select:focus {
            border-color: #1d6ea5;
            outline: none;
            box-shadow: 0 0 0 4px rgba(29, 110, 165, 0.12);
        }

        .gmv-select--compact select {
            min-width: 132px;
            padding: 11px 12px;
        }

        .gmv-select--toolbar select {
            min-width: 220px;
        }

        .gmv-select--full,
        .gmv-select--full select {
            min-width: 168px;
            width: 100%;
        }

        .gmv-period-picker {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid #d8e3ec;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(244, 249, 253, 0.94));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                0 10px 22px rgba(12, 58, 89, 0.05);
        }

        .gmv-period-picker__title {
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--casva-muted);
            font-weight: 700;
        }

        .gmv-period-picker__fields {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(132px, auto);
            gap: 10px;
            align-items: end;
        }

        .gmv-period-picker--single {
            min-width: 188px;
        }

        .gmv-period-picker--mode {
            min-width: 0;
            width: fit-content;
            flex: 0 0 auto;
        }

        .gmv-period-picker--mode .gmv-period-group {
            width: auto;
            min-width: 164px;
        }

        .gmv-period-picker__fields--single {
            grid-auto-columns: minmax(168px, 1fr);
        }

        .gmv-topbar__actions {
            display: inline-flex;
            align-items: flex-end;
            gap: 12px;
            align-self: flex-end;
        }

        .gmv-topbar__toggle {
            display: flex;
            align-items: flex-end;
        }

        .gmv-period-group {
            display: grid;
            grid-template-columns: repeat(var(--gmv-period-count, 2), minmax(0, 1fr));
            align-items: stretch;
            background: linear-gradient(180deg, #ffffff, #f6fbff);
            border: 1px solid #d1dfeb;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(12, 58, 89, 0.04);
            min-height: 48px;
            width: 100%;
        }

        .gmv-period-btn,
        .gmv-apply {
            background: transparent;
            border: 0;
            color: #5e7690;
            cursor: pointer;
            font-family: inherit;
        }

        .gmv-period-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            width: 100%;
            padding: 12px 16px;
            font-size: 11px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 700;
            white-space: nowrap;
        }

        .gmv-period-btn.is-active {
            background: linear-gradient(135deg, var(--casva-blue), #2a86c2);
            color: #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28);
        }

        .gmv-apply {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--casva-blue), #2a86c2);
            color: #fff;
            border-radius: 14px;
            min-height: 48px;
            padding: 12px 18px;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(29, 110, 165, 0.18);
            align-self: flex-end;
        }

        .gmv-compare-toggle {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 48px;
            padding: 0 2px;
            cursor: pointer;
            user-select: none;
            color: var(--casva-text);
            font-weight: 700;
            margin: 0;
        }

        .gmv-compare-toggle--action {
            min-height: 48px;
            padding: 0 18px;
            border-radius: 14px;
            border: 1px solid #d8e3ec;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(244, 249, 253, 0.94));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                0 10px 22px rgba(12, 58, 89, 0.05);
        }

        .gmv-compare-toggle input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .gmv-compare-toggle__track {
            position: relative;
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: linear-gradient(180deg, #d9e6f1, #c8d8e6);
            box-shadow: inset 0 1px 2px rgba(12, 58, 89, 0.12);
            transition: background 0.2s ease;
        }

        .gmv-compare-toggle__thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 4px 10px rgba(12, 58, 89, 0.2);
            transition: transform 0.2s ease;
        }

        .gmv-compare-toggle input:checked + .gmv-compare-toggle__track {
            background: linear-gradient(135deg, #f0b24f, #d89122);
        }

        .gmv-compare-toggle input:checked + .gmv-compare-toggle__track .gmv-compare-toggle__thumb {
            transform: translateX(20px);
        }

        .gmv-compare-toggle__label {
            font-size: 13px;
            line-height: 1;
        }

        .gmv-kpi-row,
        .gmv-grid {
            display: grid;
            gap: 16px;
        }

        .gmv-kpi-row {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .gmv-grid--single {
            grid-template-columns: 1fr;
            margin-bottom: 16px;
        }

        .gmv-grid--split {
            grid-template-columns: minmax(0, 1.4fr) minmax(340px, 0.92fr);
            margin-bottom: 16px;
        }

        .gmv-kpi,
        .gmv-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #d7e4ef;
            border-radius: 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 36px rgba(12, 58, 89, 0.06);
        }

        .gmv-kpi {
            --kpi-glow: rgba(29, 110, 165, 0.12);
            padding: 22px;
            min-height: 164px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                radial-gradient(circle at top right, var(--kpi-glow), transparent 48%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 251, 254, 0.98));
        }

        .gmv-kpi:before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            opacity: 0.82;
        }

        .gmv-kpi:after {
            content: '';
            position: absolute;
            top: -14px;
            right: -8px;
            width: 92px;
            height: 92px;
            border-radius: 26px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.62), rgba(255, 255, 255, 0.06));
            border: 1px solid rgba(255, 255, 255, 0.82);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.94);
            opacity: 0.85;
            transform: rotate(16deg);
        }

        .gmv-kpi--primary { --kpi-glow: rgba(29, 110, 165, 0.16); }
        .gmv-kpi--teal { --kpi-glow: rgba(34, 154, 150, 0.14); }
        .gmv-kpi--purple { --kpi-glow: rgba(106, 120, 209, 0.14); }
        .gmv-kpi--compare { --kpi-glow: rgba(240, 178, 79, 0.16); }
        .gmv-kpi--green { --kpi-glow: rgba(45, 179, 122, 0.16); }

        .gmv-kpi--primary:before { background: #1d6ea5; }
        .gmv-kpi--teal:before { background: #229a96; }
        .gmv-kpi--purple:before { background: #6a78d1; }
        .gmv-kpi--compare:before { background: #f0b24f; }
        .gmv-kpi--green:before { background: #2db37a; }

        .gmv-kpi__value {
            font-size: clamp(24px, 2vw, 32px);
            font-weight: 800;
            line-height: 1.08;
            margin: 12px 0;
            letter-spacing: -0.02em;
            max-width: calc(100% - 42px);
            font-variant-numeric: tabular-nums;
        }

        .gmv-kpi__compare {
            display: grid;
            grid-template-columns: max-content minmax(0, 1fr);
            column-gap: 12px;
            gap: 8px;
            align-items: start;
            font-size: 12px;
            color: #6f84a0;
            margin-top: auto;
        }

        .gmv-kpi__compare-copy {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            padding-top: 0;
        }

        .gmv-kpi__compare-copy > span {
            display: block;
            line-height: 1.45;
        }

        .gmv-kpi__compare-copy > span:first-child {
            min-height: 32px;
            display: flex;
            align-items: center;
        }

        .gmv-kpi__difference {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.35;
            font-variant-numeric: tabular-nums;
        }

        .gmv-kpi__delta,
        .gmv-delta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: start;
            min-width: 74px;
            min-height: 32px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            padding: 6px 12px;
            text-align: center;
            white-space: nowrap;
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.01em;
        }

        .is-up { background: rgba(52, 211, 142, 0.1); color: #20a96b; }
        .is-down { background: rgba(240, 75, 75, 0.1); color: #df4444; }
        .is-neutral { background: rgba(138, 160, 184, 0.14); color: #8aa0b8; }

        .gmv-kpi__value.tone-up,
        .gmv-kpi__difference.tone-up {
            color: #20a96b;
        }

        .gmv-kpi__value.tone-down,
        .gmv-kpi__difference.tone-down {
            color: #df4444;
        }

        .gmv-kpi__value.tone-default {
            color: #17324b;
        }

        .gmv-kpi__difference.tone-default {
            color: #6f84a0;
        }

        .gmv-card {
            padding: 22px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 251, 254, 0.98));
        }

        .gmv-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 22px;
            right: 22px;
            height: 1px;
            background: linear-gradient(90deg, rgba(29, 110, 165, 0.2), rgba(29, 110, 165, 0), rgba(240, 178, 79, 0.28));
        }

        .gmv-card__header {
            gap: 16px;
            margin-bottom: 16px;
            align-items: flex-start;
        }

        .gmv-card__toolbar {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            margin-left: auto;
        }

        .gmv-card__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid #dce7f1;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .gmv-card__eyebrow:before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: linear-gradient(180deg, var(--casva-blue), var(--casva-gold));
            flex: 0 0 6px;
        }

        .gmv-card__title {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
            margin: 6px 0 0;
            color: var(--casva-blue-deep);
        }

        .gmv-card__note {
            margin-top: 8px;
            color: var(--casva-muted);
            font-size: 12px;
            line-height: 1.45;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid #dde8f1;
        }

        .gmv-card__note:before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--casva-blue-soft);
        }

        .gmv-legend {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            color: #6a7f97;
            font-size: 12px;
        }

        .gmv-legend span {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .gmv-legend i {
            display: inline-block;
            width: 18px;
            height: 3px;
            border-radius: 999px;
        }

        .gmv-legend .is-current { background: #1d6ea5; }
        .gmv-legend .is-previous { background: #f0b24f; }

        .gmv-card__canvas {
            position: relative;
            min-height: 220px;
            background:
                linear-gradient(180deg, rgba(245, 249, 253, 0.96), rgba(255, 255, 255, 0.98)),
                repeating-linear-gradient(90deg, rgba(29, 110, 165, 0.02), rgba(29, 110, 165, 0.02) 1px, transparent 1px, transparent 42px);
            border: 1px solid #e3edf5;
            border-radius: 20px;
            padding: 16px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.75),
                0 10px 24px rgba(12, 58, 89, 0.03);
        }

        .gmv-card__canvas--small {
            min-height: 242px;
            width: 100%;
            max-width: 260px;
            margin: 0 auto;
        }

        .gmv-card__canvas--medium {
            min-height: 230px;
        }

        .gmv-card__canvas--donut {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #fbfdff, #f1f7fc);
        }

        .gmv-donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            pointer-events: none;
            padding: 0 52px;
        }

        .gmv-donut-center strong {
            display: block;
            font-size: 22px;
            line-height: 1.25;
            color: #10304a;
            margin-top: 6px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .gmv-donut-center small {
            margin-top: 6px;
            color: #5f7690;
            font-size: 12px;
            font-weight: 600;
        }

        .gmv-payment-layout {
            display: grid;
            grid-template-columns: minmax(238px, 280px) minmax(0, 1fr);
            gap: 18px;
            align-items: stretch;
        }

        .gmv-payment-hero {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .gmv-payment-hero__foot {
            background: linear-gradient(180deg, rgba(29, 110, 165, 0.06), rgba(29, 110, 165, 0.11));
            border: 1px solid rgba(29, 110, 165, 0.12);
            border-radius: 16px;
            padding: 14px 16px;
            color: #56718d;
            font-size: 12px;
            line-height: 1.6;
        }

        .gmv-payment-stack,
        .gmv-origin-list,
        .gmv-route-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-width: 0;
        }

        .gmv-payment-card {
            --payment-color: #1d6ea5;
            --payment-end: #4ca1d1;
            --payment-shadow: rgba(29, 110, 165, 0.16);
            --payment-glow: rgba(29, 110, 165, 0.12);
            background: linear-gradient(180deg, #ffffff, #f8fbfe);
            border: 1px solid #dce7f0;
            border-radius: 20px;
            padding: 16px 18px;
            box-shadow: 0 12px 24px rgba(12, 58, 89, 0.04);
            position: relative;
            overflow: hidden;
        }

        .gmv-payment-card:before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, var(--payment-color), var(--payment-end));
        }

        .gmv-payment-card--primary {
            --payment-color: #1d6ea5;
            --payment-end: #4ca1d1;
            --payment-shadow: rgba(29, 110, 165, 0.16);
            --payment-glow: rgba(29, 110, 165, 0.14);
        }

        .gmv-payment-card--compare {
            --payment-color: #d89122;
            --payment-end: #f0b24f;
            --payment-shadow: rgba(240, 178, 79, 0.16);
            --payment-glow: rgba(240, 178, 79, 0.14);
        }

        .gmv-payment-card--teal {
            --payment-color: #229a96;
            --payment-end: #47c1bc;
            --payment-shadow: rgba(34, 154, 150, 0.16);
            --payment-glow: rgba(34, 154, 150, 0.14);
        }

        .gmv-payment-card--purple {
            --payment-color: #6a78d1;
            --payment-end: #8e99ea;
            --payment-shadow: rgba(106, 120, 209, 0.16);
            --payment-glow: rgba(106, 120, 209, 0.14);
        }

        .gmv-payment-card--green {
            --payment-color: #2db37a;
            --payment-end: #53d39b;
            --payment-shadow: rgba(45, 179, 122, 0.16);
            --payment-glow: rgba(45, 179, 122, 0.14);
        }

        .gmv-payment-card__label {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .gmv-payment-card__label strong {
            min-width: 0;
            font-size: 15px;
            color: #10304a;
        }

        .gmv-payment-card__dot {
            width: 12px;
            height: 12px;
            flex: 0 0 12px;
            border-radius: 50%;
            background: var(--payment-color);
            box-shadow: 0 0 0 7px var(--payment-glow);
        }

        .gmv-payment-card__value {
            font-size: clamp(24px, 2vw, 28px);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--payment-color);
            overflow-wrap: anywhere;
        }

        .gmv-payment-card__meta {
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
            color: #6a7f97;
            font-size: 12px;
        }

        .gmv-payment-card__bar,
        .gmv-origin-item__bar,
        .gmv-route-item__bar {
            height: 10px;
            border-radius: 999px;
            background: #edf3f8;
            overflow: hidden;
        }

        .gmv-payment-card__bar {
            margin-top: 12px;
        }

        .gmv-payment-card__bar span,
        .gmv-origin-item__bar span,
        .gmv-route-item__bar span {
            display: block;
            height: 100%;
            border-radius: 999px;
        }

        .gmv-payment-card__bar span {
            background: linear-gradient(90deg, var(--payment-color), var(--payment-end));
            box-shadow: 0 5px 14px var(--payment-shadow);
        }

        .gmv-soft-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: linear-gradient(180deg, #f7fbfe, #eef6fb);
            border: 1px solid #d7e4ef;
            color: #58728e;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .gmv-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border-radius: 14px;
            border: 1px solid #d2e0ec;
            background: linear-gradient(180deg, #ffffff, #e8f1f8);
            color: var(--casva-blue);
            font-size: 11px;
            font-family: inherit;
            font-weight: 700;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .gmv-origin-list {
            margin-top: 14px;
        }

        .gmv-origin-item,
        .gmv-route-item {
            background:
                linear-gradient(180deg, #ffffff, #f9fbfe);
            border: 1px solid #e1ebf4;
            border-radius: 20px;
            padding: 15px 17px;
            box-shadow: 0 12px 24px rgba(12, 58, 89, 0.04);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            position: relative;
            overflow: hidden;
        }

        .gmv-origin-item:before,
        .gmv-route-item:before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
        }

        .gmv-origin-item:before {
            background: linear-gradient(180deg, var(--casva-teal), #47c1bc);
        }

        .gmv-route-item:before {
            background: linear-gradient(180deg, var(--casva-blue), var(--casva-blue-soft));
        }

        .gmv-origin-item:hover,
        .gmv-route-item:hover,
        .gmv-payment-card:hover,
        .gmv-kpi:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(12, 58, 89, 0.07);
            border-color: #cfddea;
        }

        .gmv-origin-item__head,
        .gmv-route-item__head {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
            margin-bottom: 10px;
        }

        .gmv-origin-item__copy,
        .gmv-route-item__label {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .gmv-origin-item__copy strong,
        .gmv-route-item__label strong {
            min-width: 0;
            font-size: 15px;
            color: #10304a;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .gmv-origin-item__bar span {
            background: linear-gradient(90deg, #229a96, #47c1bc);
        }

        .gmv-origin-item__value,
        .gmv-route-item__amount {
            font-size: 22px;
            line-height: 1.1;
            font-weight: 800;
            color: #10304a;
            text-align: right;
            letter-spacing: -0.02em;
        }

        .gmv-route-item__bar span {
            background: linear-gradient(90deg, #1d6ea5, #4ca1d1);
        }

        .gmv-route-item__meta {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
            color: #6a7f97;
            font-size: 12px;
        }

        .gmv-table-wrap {
            overflow: auto;
            border: 1px solid #e3edf5;
            border-radius: 20px;
            background:
                linear-gradient(180deg, #fbfdff, #f4f9fd);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .gmv-table {
            width: 100%;
            border-collapse: collapse;
        }

        .gmv-table th,
        .gmv-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #edf2f7;
            font-size: 13px;
            color: #17324b;
            white-space: nowrap;
        }

        .gmv-table th {
            color: #6a7f97;
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-align: left;
            background: linear-gradient(180deg, #f8fcff, #eef6fb);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .gmv-table tbody tr:hover {
            background: rgba(29, 110, 165, 0.035);
        }

        .gmv-table tbody tr:nth-child(even) {
            background: rgba(248, 251, 254, 0.65);
        }

        .gmv-client-name {
            font-weight: 700;
            color: #10304a;
        }

        .gmv-company-pill {
            display: inline-flex;
            align-items: center;
            max-width: 240px;
            padding: 6px 10px;
            border-radius: 999px;
            background: linear-gradient(180deg, #f7fbfe, #eef6fb);
            border: 1px solid #d7e4ef;
            color: #55708e;
            font-size: 12px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .gmv-table td:nth-child(3) {
            font-family: inherit;
            font-weight: 700;
        }

        .gmv-empty {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(248, 251, 254, 0.94);
            border-radius: 16px;
            color: var(--casva-muted);
            font-size: 14px;
            text-align: center;
            padding: 20px;
            backdrop-filter: blur(2px);
        }

        .gmv-empty--inline,
        .gmv-empty--block {
            position: static;
            background: #f9fbfd;
            border: 1px dashed #c9d9e7;
            min-height: 100px;
        }

        @media (max-width: 1280px) {
            .gmv-topbar__insights {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .gmv-kpi-row {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .gmv-grid--split,
            .gmv-payment-layout {
                grid-template-columns: 1fr;
            }

            .gmv-card__canvas--small {
                max-width: 280px;
            }
        }

        @media (max-width: 840px) {
            .gmv-topbar__meta,
            .gmv-topbar__controls {
                width: 100%;
            }

            .gmv-topbar__main {
                flex-direction: column;
                align-items: stretch;
            }

            .gmv-topbar__controls {
                justify-content: flex-start;
            }

            .gmv-period-picker {
                width: 100%;
            }

            .gmv-topbar__actions {
                width: 100%;
                justify-content: flex-start;
            }

            .gmv-card__toolbar {
                width: 100%;
                margin-left: 0;
                justify-content: flex-start;
            }

            .gmv-kpi-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .gmv-kpi__compare {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 720px) {
            .gmv-dashboard {
                padding: 16px;
            }

            .gmv-topbar {
                padding: 16px;
            }

            .gmv-card,
            .gmv-kpi {
                padding: 18px;
            }

            .gmv-origin-item__head,
            .gmv-route-item__head {
                grid-template-columns: auto 1fr;
            }

            .gmv-origin-item__value,
            .gmv-route-item__amount {
                grid-column: 1 / -1;
                text-align: left;
                padding-left: 46px;
            }

            .gmv-route-item__meta {
                justify-content: flex-start;
            }
        }

        @media (max-width: 560px) {
            .gmv-topbar__insights {
                grid-template-columns: 1fr;
            }

            .gmv-kpi-row {
                grid-template-columns: 1fr;
            }

            .gmv-select select,
            .gmv-topbar__controls,
            .gmv-period-group,
            .gmv-apply {
                width: 100%;
            }

            .gmv-topbar__actions,
            .gmv-topbar__toggle,
            .gmv-compare-toggle--action {
                width: 100%;
            }

            .gmv-card__toolbar,
            .gmv-select--toolbar,
            .gmv-select--toolbar select {
                width: 100%;
            }

            .gmv-period-picker__fields {
                grid-auto-flow: row;
                grid-template-columns: 1fr;
            }

            .gmv-period-group {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }

            .gmv-period-btn {
                width: 100%;
            }

            .gmv-card__canvas--small {
                max-width: none;
            }

            .gmv-payment-card__head,
            .gmv-payment-card__meta,
            .gmv-card__header {
                flex-direction: column;
                align-items: flex-start;
            }

            .gmv-payment-card__head,
            .gmv-payment-card__meta {
                gap: 8px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var payloadEl = document.getElementById('dashboard-payload');
            if (!payloadEl || typeof Chart === 'undefined') {
                return;
            }

            var dashboard = JSON.parse(payloadEl.textContent || '{}');
            var compareEnabled = Number(((dashboard.filters || {}).compare || 0)) === 1;
            var fontFamily = window.getComputedStyle(document.body).fontFamily || 'sans-serif';
            var numberFormat = new Intl.NumberFormat('ru-RU');
            var colors = {
                current: '#1d6ea5',
                previous: '#f0b24f',
                teal: '#229a96',
                purple: '#6a78d1',
                green: '#2db37a',
                axis: '#6a7f97',
                grid: 'rgba(16,48,74,0.08)'
            };

            Chart.defaults.global.defaultFontFamily = fontFamily;
            Chart.defaults.global.defaultFontColor = colors.axis;
            Chart.defaults.global.legend.display = false;

            function formatMoney(value) {
                return numberFormat.format(Math.round(value || 0));
            }

            function tooltipOptions() {
                return {
                    backgroundColor: 'rgba(16, 48, 74, 0.94)',
                    titleFontFamily: fontFamily,
                    bodyFontFamily: fontFamily,
                    titleFontColor: '#ffffff',
                    bodyFontColor: '#eef5fb',
                    cornerRadius: 10,
                    displayColors: false,
                    xPadding: 12,
                    yPadding: 10
                };
            }

            function createChart(id, config) {
                var node = document.getElementById(id);
                if (!node) {
                    return null;
                }
                return new Chart(node.getContext('2d'), config);
            }

            function verticalGradient(node, topColor, bottomColor) {
                if (!node) {
                    return topColor;
                }

                var context = node.getContext('2d');
                var gradient = context.createLinearGradient(0, 0, 0, node.height || 220);
                gradient.addColorStop(0, topColor);
                gradient.addColorStop(1, bottomColor);

                return gradient;
            }

            var mainChartNode = document.getElementById('dashboard-main-chart');
            var secondaryChartNode = document.getElementById('dashboard-secondary-chart');
            var paymentChartNode = document.getElementById('dashboard-payment-chart');
            var originChartNode = document.getElementById('dashboard-origin-chart');
            var mainGradient = verticalGradient(mainChartNode, 'rgba(29,110,165,0.26)', 'rgba(29,110,165,0.02)');
            var secondaryCurrentGradient = verticalGradient(secondaryChartNode, 'rgba(29,110,165,0.82)', 'rgba(29,110,165,0.28)');
            var secondaryPreviousGradient = verticalGradient(secondaryChartNode, 'rgba(240,178,79,0.62)', 'rgba(240,178,79,0.18)');
            var originGradient = verticalGradient(originChartNode, 'rgba(34,154,150,0.82)', 'rgba(34,154,150,0.28)');

            createChart('dashboard-main-chart', {
                type: 'line',
                data: {
                    labels: (dashboard.charts.main || {}).labels || [],
                    datasets: [
                        {
                            label: 'Текущий период',
                            data: (dashboard.charts.main || {}).current || [],
                            borderColor: colors.current,
                            backgroundColor: mainGradient,
                            borderWidth: 4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: colors.current,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: colors.current,
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3,
                            lineTension: 0.34,
                            fill: true
                        }
                    ].concat(compareEnabled ? [{
                        label: 'Предыдущий период',
                        data: (dashboard.charts.main || {}).previous || [],
                        borderColor: colors.previous,
                        borderDash: [6, 4],
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: colors.previous,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        lineTension: 0.28,
                        fill: false
                    }] : [])
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {display: false},
                    tooltips: Object.assign(tooltipOptions(), {callbacks: {label: function (item) { return formatMoney(item.yLabel); }}}),
                    scales: {
                        xAxes: [{
                            gridLines: {display: false},
                            ticks: {
                                fontColor: colors.axis,
                                fontSize: 11,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: colors.grid,
                                zeroLineColor: 'rgba(16,48,74,0.12)',
                                borderDash: [4, 4]
                            },
                            ticks: {
                                fontColor: colors.axis,
                                fontSize: 11,
                                padding: 10,
                                callback: function (value) { return numberFormat.format(value); }
                            }
                        }]
                    }
                }
            });

            createChart('dashboard-secondary-chart', {
                type: 'bar',
                data: {
                    labels: (dashboard.charts.secondary || {}).labels || [],
                    datasets: (compareEnabled ? [{
                        label: 'Предыдущий',
                        data: (dashboard.charts.secondary || {}).previous || [],
                        backgroundColor: secondaryPreviousGradient,
                        borderColor: colors.previous,
                        borderWidth: 1,
                        categoryPercentage: 0.62,
                        barPercentage: 0.82
                    }] : []).concat([{
                        label: 'Текущий',
                        data: (dashboard.charts.secondary || {}).current || [],
                        backgroundColor: secondaryCurrentGradient,
                        borderColor: colors.current,
                        borderWidth: 1,
                        categoryPercentage: 0.62,
                        barPercentage: 0.82
                    }])
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: Object.assign(tooltipOptions(), {callbacks: {label: function (item) { return formatMoney(item.yLabel); }}}),
                    scales: {
                        xAxes: [{
                            gridLines: {display: false},
                            ticks: {
                                fontColor: colors.axis,
                                fontSize: 11,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: colors.grid,
                                zeroLineColor: 'rgba(16,48,74,0.12)',
                                borderDash: [4, 4]
                            },
                            ticks: {
                                fontColor: colors.axis,
                                fontSize: 11,
                                padding: 10,
                                callback: function (value) { return numberFormat.format(value); }
                            }
                        }]
                    }
                }
            });

            createChart('dashboard-payment-chart', {
                type: 'doughnut',
                data: {
                    labels: (dashboard.charts.payments || {}).labels || [],
                    datasets: [{
                        data: (dashboard.charts.payments || {}).values || [],
                        backgroundColor: [colors.current, colors.previous, colors.teal, colors.purple, colors.green],
                        borderWidth: 5,
                        borderColor: '#f6fbff',
                        hoverBorderColor: '#ffffff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutoutPercentage: 72,
                    tooltips: Object.assign(tooltipOptions(), {callbacks: {label: function (item, data) { return data.labels[item.index] + ': ' + formatMoney(data.datasets[0].data[item.index]); }}})
                }
            });

            createChart('dashboard-origin-chart', {
                type: 'bar',
                data: {
                    labels: (dashboard.charts.origins || {}).labels || [],
                    datasets: [{
                        data: (dashboard.charts.origins || {}).values || [],
                        backgroundColor: originGradient,
                        borderColor: colors.teal,
                        borderWidth: 1,
                        categoryPercentage: 0.58,
                        barPercentage: 0.78
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: Object.assign(tooltipOptions(), {callbacks: {label: function (item) { return formatMoney(item.yLabel); }}}),
                    scales: {
                        xAxes: [{
                            gridLines: {display: false},
                            ticks: {
                                fontColor: colors.axis,
                                fontSize: 11,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: colors.grid,
                                zeroLineColor: 'rgba(16,48,74,0.12)',
                                borderDash: [4, 4]
                            },
                            ticks: {
                                fontColor: colors.axis,
                                fontSize: 11,
                                padding: 10,
                                callback: function (value) { return numberFormat.format(value); }
                            }
                        }]
                    }
                }
            });

            var filterForm = document.getElementById('dashboard-filters');
            var periodInput = document.getElementById('dashboard-period-input');
            if (filterForm) {
                var periodButtons = filterForm.querySelectorAll('.gmv-period-btn');
                Array.prototype.forEach.call(periodButtons, function (button) {
                    button.addEventListener('click', function () {
                        periodInput.value = button.getAttribute('data-period');
                        filterForm.submit();
                    });
                });
            }

            var clientGroupSelects = document.querySelectorAll('.js-client-group-select');
            Array.prototype.forEach.call(clientGroupSelects, function (select) {
                select.addEventListener('change', function () {
                    if (select.form) {
                        select.form.submit();
                    }
                });
            });
        })();
    </script>
@endpush
