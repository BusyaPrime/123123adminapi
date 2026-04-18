@extends('admin.layout')

@php
    $dashboard = $funnelDashboard ?? [];
    $filters = $dashboard['filters'] ?? [];
    $options = $dashboard['options'] ?? ['ranges' => [], 'platforms' => []];
    $range = $dashboard['range'] ?? [];
    $hero = $dashboard['hero'] ?? [];
    $kpis = $dashboard['kpis'] ?? [];
    $charts = $dashboard['charts'] ?? [];
    $steps = $dashboard['steps'] ?? [];
    $dropoff = $dashboard['dropoff'] ?? ['items' => [], 'bottleneck' => null];
    $stuck = $dashboard['stuck'] ?? ['items' => []];
    $summary = $dashboard['summary'] ?? [];
    $weeklyChart = $charts['weekly_conversion'] ?? [];
    $timeChart = $charts['time_to_complete'] ?? [];
    $dropoffChart = $charts['dropoff'] ?? [];
    $stuckGauge = $charts['stuck_gauge'] ?? [];
    $dropoffItems = $dropoff['items'] ?? [];
    $bottleneck = $dropoff['bottleneck'] ?? null;
    $hasData = !empty($dashboard['has_data']);
    $hasAggregates = !empty($dashboard['has_aggregates']);

    $stepColors = [
        'route_filled' => '#2f7bff',
        'cargo_filled' => '#5f73ff',
        'price_shown' => '#f0a54b',
        'completed' => '#27b27d',
    ];

    $dropoffColors = [
        '#ff6b7d',
        '#f4a64d',
        '#6a7ff2',
        '#2aa7a4',
    ];

    $severityLabels = [
        'critical' => 'Высокая потеря',
        'warning' => 'Нужно внимание',
        'stable' => 'Шаг стабилен',
    ];

    $formatNumber = function ($value) {
        return number_format((int) $value, 0, '.', ' ');
    };

    $formatPercent = function ($value) {
        return number_format((float) $value, 2, '.', ' ') . '%';
    };

    $formatMinutes = function ($value) {
        return number_format((float) $value, 1, '.', ' ') . ' мин';
    };

    $formatKpi = function ($item) use ($formatNumber, $formatPercent) {
        return ($item['type'] ?? 'number') === 'percent'
            ? $formatPercent($item['value'] ?? 0)
            : $formatNumber($item['value'] ?? 0);
    };
@endphp

@section('center_content')
    @component('component.card', ['title' => 'Дашборды'])
        <div class="funnel-dashboard">
            <form class="funnel-hero" action="{{ route('admin.dashboards.funnel') }}" method="GET">
                <div class="funnel-hero__top">
                    <div class="funnel-hero__copy">
                        <div class="funnel-hero__eyebrow">{{ $hero['eyebrow'] ?? 'Статистика по воронке заказа' }}</div>
                        <h2 class="funnel-hero__title">{{ $hero['title'] ?? 'Воронка' }}</h2>
                        <p class="funnel-hero__subtitle">{{ $hero['subtitle'] ?? '' }}</p>
                        <p class="funnel-hero__descriptor">{{ $hero['descriptor'] ?? '' }}</p>
                    </div>

                    <div class="funnel-filters">
                        <label class="funnel-field">
                            <span>Период</span>
                            <select name="range">
                                @foreach(($options['ranges'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['range'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="funnel-field">
                            <span>Платформа</span>
                            <select name="platform">
                                @foreach(($options['platforms'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['platform'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="funnel-field">
                            <span>Дата от</span>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                        </label>

                        <label class="funnel-field">
                            <span>Дата до</span>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                        </label>

                        <button type="submit" class="funnel-submit">Применить</button>
                    </div>
                </div>

                <div class="funnel-hero__meta">
                    <span class="funnel-meta-chip">Период: {{ $range['label'] ?? '-' }}</span>
                    <span class="funnel-meta-chip">Данные по: {{ $range['latest_aggregate_label'] ?? 'Нет данных' }}</span>
                    <span class="funnel-meta-chip">Обновлено: {{ $dashboard['generated_at'] ?? '-' }}</span>
                </div>

                <div class="funnel-summary-grid">
                    @foreach($kpis as $index => $kpi)
                        <article class="funnel-summary-card funnel-summary-card--{{ $index + 1 }}">
                            <span class="funnel-summary-card__label">{{ $kpi['label'] }}</span>
                            <strong class="funnel-summary-card__value">{{ $formatKpi($kpi) }}</strong>
                            <span class="funnel-summary-card__hint">{{ $kpi['hint'] }}</span>
                        </article>
                    @endforeach
                </div>
            </form>

            @if(!$hasAggregates)
                <div class="funnel-empty funnel-empty--top">
                    Статистика пока недоступна. Она появится после накопления данных.
                </div>
            @endif

            <section class="funnel-card funnel-card--wide">
                <div class="funnel-card__header">
                    <div>
                        <div class="funnel-card__eyebrow">Динамика</div>
                        <h3 class="funnel-card__title">Конверсия по шагам — Динамика по неделям</h3>
                        <p class="funnel-card__description">График показывает, как меняется прохождение шагов по неделям.</p>
                    </div>
                    <div class="funnel-legend">
                        @foreach(($weeklyChart['datasets'] ?? []) as $dataset)
                            <span>
                                <i style="background: {{ $stepColors[$dataset['name']] ?? '#2f7bff' }};"></i>
                                {{ $dataset['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="funnel-chart-shell">
                    <canvas id="funnel-weekly-chart" height="120"></canvas>
                    @unless($hasData)
                        <div class="funnel-empty">Нет данных по воронке за выбранный период.</div>
                    @endunless
                </div>
            </section>

            <div class="funnel-grid funnel-grid--equal">
                <section class="funnel-card">
                    <div class="funnel-card__header">
                        <div>
                            <div class="funnel-card__eyebrow">Текущий период</div>
                            <h3 class="funnel-card__title">Шаги воронки — текущий период</h3>
                            <p class="funnel-card__description">Здесь видно, сколько пользователей дошли до каждого шага и где сильнее всего просадка.</p>
                        </div>
                    </div>

                    <div class="funnel-steps-list">
                        @forelse($steps as $step)
                            <article class="funnel-step-card funnel-step-card--{{ $step['severity'] }}">
                                <div class="funnel-step-card__head">
                                    <div class="funnel-step-card__identity">
                                        <span class="funnel-step-card__number">{{ $step['number'] }}</span>
                                        <div>
                                            <h4>{{ $step['label'] }}</h4>
                                            <p>{{ $step['description'] }}</p>
                                        </div>
                                    </div>
                                    <div class="funnel-step-card__metrics">
                                        <strong>{{ $formatPercent($step['reached_pct']) }}</strong>
                                        <span>{{ $severityLabels[$step['severity']] ?? 'Шаг' }}</span>
                                    </div>
                                </div>

                                <div class="funnel-step-card__body">
                                    <div class="funnel-step-card__stat">
                                        <small>Дошли</small>
                                        <strong>{{ $formatNumber($step['reached']) }}</strong>
                                    </div>
                                    <div class="funnel-step-card__stat is-danger">
                                        <small>Просадка между шагами</small>
                                        <strong>{{ $formatPercent($step['loss_pct']) }}</strong>
                                        <span>{{ $formatNumber($step['loss_count']) }} пользователей</span>
                                    </div>
                                    <div class="funnel-step-card__stat">
                                        <small>Брошено / в процессе</small>
                                        <strong>{{ $formatNumber($step['dropped'] + $step['stuck']) }}</strong>
                                        <span>{{ $formatNumber($step['stuck']) }} застряли</span>
                                    </div>
                                </div>

                                <div class="funnel-step-card__progress">
                                    <span style="width: {{ min(100, max(0, $step['reached_pct'])) }}%; background: {{ $stepColors[$step['name']] ?? '#2f7bff' }};"></span>
                                </div>
                            </article>
                        @empty
                            <div class="funnel-empty funnel-empty--inline">За выбранный период шаги пока не заполнены.</div>
                        @endforelse
                    </div>
                </section>

                <section class="funnel-card">
                    <div class="funnel-card__header">
                        <div>
                            <div class="funnel-card__eyebrow">Потери</div>
                            <h3 class="funnel-card__title">Где теряются пользователи</h3>
                            <p class="funnel-card__description">Здесь видно, на каком шаге чаще всего останавливаются пользователи.</p>
                        </div>
                    </div>

                    <div class="funnel-dropoff-layout">
                        <div class="funnel-dropoff-layout__chart">
                            <canvas id="funnel-dropoff-chart" height="200"></canvas>
                            @unless(count($dropoffItems))
                                <div class="funnel-empty">За выбранный период потерь не найдено.</div>
                            @endunless
                        </div>

                        <div class="funnel-dropoff-layout__legend">
                            @forelse($dropoffItems as $index => $item)
                                <div class="funnel-dropoff-item">
                                    <div class="funnel-dropoff-item__head">
                                        <span class="funnel-dropoff-item__dot" style="background: {{ $dropoffColors[$index % count($dropoffColors)] }};"></span>
                                        <strong>{{ $item['label'] }}</strong>
                                    </div>
                                    <div class="funnel-dropoff-item__meta">
                                        <span>{{ $formatPercent($item['share_pct']) }}</span>
                                        <span>{{ $formatNumber($item['value']) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="funnel-empty funnel-empty--inline">За выбранный период потерь не найдено.</div>
                            @endforelse
                        </div>
                    </div>

                    @if($bottleneck)
                        <div class="funnel-callout">
                            <div class="funnel-callout__eyebrow">Главный проблемный шаг</div>
                            <strong>{{ $bottleneck['label'] }} — {{ $formatPercent($bottleneck['share_pct']) }} всех потерь</strong>
                            <p>{{ $bottleneck['interpretation'] }}</p>
                        </div>
                    @endif
                </section>
            </div>

            <div class="funnel-grid funnel-grid--diagnostics">
                <section class="funnel-card">
                    <div class="funnel-card__header">
                        <div>
                            <div class="funnel-card__eyebrow">Время</div>
                            <h3 class="funnel-card__title">Среднее время оформления заказа</h3>
                            <p class="funnel-card__description">Показывает, сколько в среднем времени занимает путь до завершения.</p>
                        </div>
                        <div class="funnel-card__stats">
                            <span>Среднее <strong>{{ $formatMinutes($summary['avg_completion_minutes'] ?? 0) }}</strong></span>
                            <span>Медиана <strong>{{ $formatMinutes($summary['median_completion_minutes'] ?? 0) }}</strong></span>
                        </div>
                    </div>

                    <div class="funnel-chart-shell">
                        <canvas id="funnel-time-chart" height="120"></canvas>
                        @unless($hasData)
                            <div class="funnel-empty">За выбранный период завершений пока нет.</div>
                        @endunless
                    </div>
                </section>

                <section class="funnel-card">
                    <div class="funnel-card__header">
                        <div>
                            <div class="funnel-card__eyebrow">Незавершенные</div>
                            <h3 class="funnel-card__title">Не завершили</h3>
                            <p class="funnel-card__description">Сколько пользователей не дошли до конца и на каких шагах чаще всего останавливаются.</p>
                        </div>
                    </div>

                    <div class="funnel-gauge">
                        <div class="funnel-gauge__chart">
                            <canvas id="funnel-stuck-gauge" height="180"></canvas>
                            <div class="funnel-gauge__center">
                                <strong>{{ $formatNumber($stuck['unresolved_total'] ?? 0) }}</strong>
                                <span>не завершили</span>
                                <small>{{ $formatPercent($stuck['unresolved_pct'] ?? 0) }} от стартов</small>
                            </div>
                        </div>

                        <div class="funnel-gauge__chips">
                            <span class="funnel-chip funnel-chip--warning">В процессе: {{ $formatNumber($stuck['total'] ?? 0) }}</span>
                            <span class="funnel-chip funnel-chip--danger">Брошено: {{ $formatNumber($stuck['abandoned_total'] ?? 0) }}</span>
                        </div>
                    </div>

                    <div class="funnel-stuck-list">
                        @forelse(($stuck['items'] ?? []) as $item)
                            <div class="funnel-stuck-item">
                                <div class="funnel-stuck-item__top">
                                    <span>{{ $item['label'] }}</span>
                                    <strong>{{ $formatNumber($item['value']) }}</strong>
                                </div>
                                <div class="funnel-stuck-item__meta">
                                    <span>{{ $formatPercent($item['started_share']) }} от стартов</span>
                                    <span>{{ $formatPercent($item['share']) }} от всех in progress</span>
                                </div>
                                <div class="funnel-stuck-item__bar">
                                    <span style="width: {{ min(100, max(8, $item['share'])) }}%;"></span>
                                </div>
                            </div>
                        @empty
                            <div class="funnel-empty funnel-empty--inline">За выбранный период незавершенных пользователей нет.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <script id="funnel-dashboard-payload" type="application/json">
            {!! json_encode($dashboard, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
        </script>
    @endcomponent
@endsection

@push('styles')
    <style>
        .funnel-dashboard {
            padding: 18px;
            border-radius: 26px;
            background:
                radial-gradient(circle at top left, rgba(94, 135, 255, 0.10), transparent 22%),
                radial-gradient(circle at right center, rgba(32, 182, 175, 0.10), transparent 20%),
                linear-gradient(180deg, #eef5fb 0%, #e6eff7 100%);
            color: #11324a;
        }

        .funnel-hero,
        .funnel-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            border: 1px solid #d7e4ef;
            background: linear-gradient(180deg, #ffffff, #f7fbff);
            box-shadow: 0 18px 42px rgba(24, 55, 84, 0.08);
        }

        .funnel-hero::before,
        .funnel-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 24px;
            right: 24px;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, #2f7bff 0%, #2aa7a4 55%, #f0a54b 100%);
            opacity: 0.9;
        }

        .funnel-hero {
            padding: 28px 28px 24px;
            margin-bottom: 20px;
        }

        .funnel-hero__top {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(320px, 0.95fr);
            gap: 24px;
            align-items: start;
        }

        .funnel-hero__eyebrow,
        .funnel-card__eyebrow,
        .funnel-callout__eyebrow {
            display: inline-block;
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #6c829a;
            margin-bottom: 10px;
        }

        .funnel-hero__title {
            margin: 0;
            font-size: 36px;
            line-height: 1.05;
            font-weight: 800;
            color: #10304a;
        }

        .funnel-hero__subtitle,
        .funnel-hero__descriptor,
        .funnel-card__description {
            margin: 0;
            font-size: 14px;
            line-height: 1.65;
            color: #647b92;
        }

        .funnel-hero__subtitle {
            margin-top: 10px;
        }

        .funnel-hero__descriptor {
            margin-top: 6px;
            max-width: 680px;
        }

        .funnel-filters {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            padding: 18px;
            border-radius: 20px;
            border: 1px solid #e2ecf4;
            background: linear-gradient(180deg, #fbfdff, #f2f7fb);
        }

        .funnel-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .funnel-field span {
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #6c829a;
        }

        .funnel-field select,
        .funnel-field input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border-radius: 14px;
            border: 1px solid #d3e1ec;
            background: #ffffff;
            color: #10304a;
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .funnel-submit {
            height: 46px;
            align-self: end;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, #2f7bff, #2aa7a4);
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.04em;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(47, 123, 255, 0.18);
        }

        .funnel-hero__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .funnel-meta-chip,
        .funnel-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid #d9e6f0;
            background: rgba(255, 255, 255, 0.8);
            color: #57708c;
            font-size: 12px;
            font-weight: 600;
        }

        .funnel-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .funnel-summary-card {
            padding: 18px 20px;
            border-radius: 20px;
            border: 1px solid #deebf5;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.95), transparent 38%),
                linear-gradient(180deg, #ffffff, #f4f9fd);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .funnel-summary-card--1 {
            box-shadow: inset 0 3px 0 rgba(47, 123, 255, 0.84), 0 14px 28px rgba(47, 123, 255, 0.10);
        }

        .funnel-summary-card--2 {
            box-shadow: inset 0 3px 0 rgba(42, 167, 164, 0.84), 0 14px 28px rgba(42, 167, 164, 0.10);
        }

        .funnel-summary-card--3 {
            box-shadow: inset 0 3px 0 rgba(240, 165, 75, 0.84), 0 14px 28px rgba(240, 165, 75, 0.10);
        }

        .funnel-summary-card__label {
            display: block;
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #6c829a;
        }

        .funnel-summary-card__value {
            display: block;
            margin-top: 10px;
            font-size: 38px;
            line-height: 1;
            font-weight: 800;
            color: #10304a;
        }

        .funnel-summary-card__hint {
            display: block;
            margin-top: 10px;
            color: #678099;
            font-size: 13px;
            line-height: 1.5;
        }

        .funnel-grid {
            display: grid;
            gap: 18px;
            margin-top: 18px;
        }

        .funnel-grid--equal {
            grid-template-columns: 1fr 1fr;
        }

        .funnel-grid--diagnostics {
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.75fr);
        }

        .funnel-card {
            padding: 24px;
        }

        .funnel-card--wide {
            margin-top: 18px;
        }

        .funnel-card__header {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .funnel-card__title {
            margin: 0 0 6px;
            font-size: 28px;
            line-height: 1.15;
            color: #10304a;
        }

        .funnel-card__stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: right;
            color: #5f7792;
            font-size: 13px;
            white-space: nowrap;
        }

        .funnel-card__stats strong {
            color: #10304a;
            margin-left: 6px;
        }

        .funnel-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px 14px;
        }

        .funnel-legend span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #627b95;
            font-size: 12px;
            font-weight: 600;
        }

        .funnel-legend i {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
        }

        .funnel-chart-shell {
            position: relative;
            min-height: 320px;
            padding: 18px;
            border-radius: 20px;
            border: 1px solid #e5eef5;
            background: linear-gradient(180deg, #fcfeff, #f3f8fc);
        }

        .funnel-dropoff-layout {
            display: grid;
            grid-template-columns: minmax(220px, 0.9fr) minmax(0, 1fr);
            gap: 18px;
            align-items: center;
        }

        .funnel-dropoff-layout__chart {
            position: relative;
            min-height: 250px;
            padding: 14px;
            border-radius: 20px;
            border: 1px solid #e5eef5;
            background: linear-gradient(180deg, #fcfeff, #f3f8fc);
        }

        .funnel-dropoff-layout__legend {
            display: grid;
            gap: 12px;
        }

        .funnel-dropoff-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid #e5eef5;
            background: rgba(255, 255, 255, 0.85);
        }

        .funnel-dropoff-item__head {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .funnel-dropoff-item__head strong {
            font-size: 14px;
            color: #10304a;
        }

        .funnel-dropoff-item__dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .funnel-dropoff-item__meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            color: #627b95;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .funnel-callout {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid #f3d6d8;
            background: linear-gradient(180deg, #fff7f8, #fff2f3);
        }

        .funnel-callout strong {
            display: block;
            color: #b5465a;
            font-size: 18px;
            margin-bottom: 6px;
        }

        .funnel-callout p {
            margin: 0;
            color: #6c7b8f;
            font-size: 13px;
            line-height: 1.6;
        }

        .funnel-steps-list,
        .funnel-stuck-list {
            display: grid;
            gap: 14px;
        }

        .funnel-step-card,
        .funnel-stuck-item {
            padding: 16px 18px;
            border-radius: 20px;
            border: 1px solid #e4edf5;
            background: linear-gradient(180deg, #ffffff, #f7fbff);
        }

        .funnel-step-card {
            position: relative;
            overflow: hidden;
        }

        .funnel-step-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 16px;
            bottom: 16px;
            width: 4px;
            border-radius: 999px;
            background: #d5e1ec;
        }

        .funnel-step-card--critical::before {
            background: #ff6b7d;
        }

        .funnel-step-card--warning::before {
            background: #f0a54b;
        }

        .funnel-step-card--stable::before {
            background: #2aa7a4;
        }

        .funnel-step-card__head,
        .funnel-step-card__body,
        .funnel-stuck-item__top,
        .funnel-stuck-item__meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }

        .funnel-step-card__identity {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            min-width: 0;
        }

        .funnel-step-card__number {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #edf4fa, #e4eef7);
            color: #2f7bff;
            font-size: 13px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .funnel-step-card__identity h4 {
            margin: 0;
            font-size: 18px;
            color: #10304a;
        }

        .funnel-step-card__identity p {
            margin: 5px 0 0;
            color: #687f98;
            font-size: 13px;
            line-height: 1.55;
        }

        .funnel-step-card__metrics {
            text-align: right;
            white-space: nowrap;
        }

        .funnel-step-card__metrics strong {
            display: block;
            font-size: 28px;
            line-height: 1;
            color: #10304a;
        }

        .funnel-step-card__metrics span {
            display: inline-flex;
            margin-top: 8px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #eff5fa;
            color: #69819b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .funnel-step-card__body {
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .funnel-step-card__stat {
            min-width: 160px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            color: #6a7f97;
            font-size: 12px;
        }

        .funnel-step-card__stat strong {
            color: #10304a;
            font-size: 18px;
        }

        .funnel-step-card__stat span {
            color: #7e93aa;
            font-size: 12px;
        }

        .funnel-step-card__stat.is-danger strong {
            color: #c75465;
        }

        .funnel-step-card__progress,
        .funnel-stuck-item__bar {
            margin-top: 16px;
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: #e7eef5;
        }

        .funnel-step-card__progress span,
        .funnel-stuck-item__bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
        }

        .funnel-stuck-item__top span {
            color: #10304a;
            font-size: 15px;
            font-weight: 700;
        }

        .funnel-stuck-item__top strong {
            color: #c75465;
            font-size: 18px;
        }

        .funnel-stuck-item__meta {
            margin-top: 8px;
            color: #6d839b;
            font-size: 12px;
        }

        .funnel-stuck-item__bar span {
            background: linear-gradient(90deg, #f0a54b, #ff6b7d);
        }

        .funnel-gauge {
            display: grid;
            gap: 16px;
        }

        .funnel-gauge__chart {
            position: relative;
            min-height: 220px;
            padding: 12px 12px 0;
            border-radius: 20px;
            border: 1px solid #e5eef5;
            background: linear-gradient(180deg, #fcfeff, #f3f8fc);
        }

        .funnel-gauge__center {
            position: absolute;
            left: 50%;
            bottom: 28px;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .funnel-gauge__center strong {
            font-size: 34px;
            line-height: 1;
            color: #10304a;
        }

        .funnel-gauge__center span {
            margin-top: 6px;
            color: #697f97;
            font-size: 13px;
            font-weight: 700;
        }

        .funnel-gauge__center small {
            margin-top: 4px;
            color: #8599ad;
            font-size: 12px;
        }

        .funnel-gauge__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .funnel-chip--warning {
            border-color: #f3e0c2;
            background: #fff7eb;
            color: #c17c18;
        }

        .funnel-chip--danger {
            border-color: #f1d3d8;
            background: #fff4f6;
            color: #c75465;
        }

        .funnel-empty {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            border-radius: 18px;
            background: rgba(250, 252, 254, 0.94);
            color: #7389a1;
            border: 1px dashed #cfdae4;
        }

        .funnel-empty--inline,
        .funnel-empty--top {
            position: static;
            min-height: 110px;
            margin-top: 18px;
        }

        @media (max-width: 1360px) {
            .funnel-hero__top,
            .funnel-grid--diagnostics {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 1140px) {
            .funnel-grid--equal,
            .funnel-dropoff-layout,
            .funnel-summary-grid {
                grid-template-columns: 1fr;
            }

            .funnel-card__header,
            .funnel-step-card__head,
            .funnel-step-card__body {
                flex-direction: column;
            }

            .funnel-legend {
                justify-content: flex-start;
            }
        }

        @media (max-width: 720px) {
            .funnel-dashboard {
                padding: 14px;
            }

            .funnel-hero,
            .funnel-card {
                padding: 18px;
            }

            .funnel-filters {
                grid-template-columns: 1fr;
            }

            .funnel-hero__title {
                font-size: 28px;
            }

            .funnel-card__title {
                font-size: 22px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var payloadNode = document.getElementById('funnel-dashboard-payload');

            if (!payloadNode || typeof Chart === 'undefined') {
                return;
            }

            var dashboard = JSON.parse(payloadNode.textContent || '{}');
            var weeklyChart = dashboard.charts ? (dashboard.charts.weekly_conversion || {}) : {};
            var timeChart = dashboard.charts ? (dashboard.charts.time_to_complete || {}) : {};
            var dropoffChart = dashboard.charts ? (dashboard.charts.dropoff || {}) : {};
            var stuckGauge = dashboard.charts ? (dashboard.charts.stuck_gauge || {}) : {};

            var numberFormat = new Intl.NumberFormat('ru-RU');
            var colors = {
                muted: '#6b8199',
                grid: 'rgba(16, 48, 74, 0.08)',
                white: '#ffffff',
                blue: '#2f7bff',
                indigo: '#5f73ff',
                orange: '#f0a54b',
                green: '#27b27d',
                coral: '#ff6b7d',
                teal: '#2aa7a4'
            };

            var stepPalette = {
                route_filled: {line: colors.blue, fill: 'rgba(47, 123, 255, 0.10)'},
                cargo_filled: {line: colors.indigo, fill: 'rgba(95, 115, 255, 0.10)'},
                price_shown: {line: colors.orange, fill: 'rgba(240, 165, 75, 0.12)'},
                completed: {line: colors.green, fill: 'rgba(39, 178, 125, 0.12)'}
            };

            function tooltipOptions() {
                return {
                    backgroundColor: 'rgba(16, 48, 74, 0.95)',
                    titleFontColor: '#ffffff',
                    bodyFontColor: '#edf5fb',
                    borderColor: 'rgba(255,255,255,0.06)',
                    borderWidth: 1,
                    cornerRadius: 10,
                    displayColors: false,
                    xPadding: 14,
                    yPadding: 12
                };
            }

            function createChart(id, config) {
                var node = document.getElementById(id);

                if (!node) {
                    return null;
                }

                return new Chart(node.getContext('2d'), config);
            }

            function areaGradient(node, color) {
                if (!node) {
                    return color;
                }

                var context = node.getContext('2d');
                var gradient = context.createLinearGradient(0, 0, 0, node.height || 220);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, 'rgba(255,255,255,0)');

                return gradient;
            }

            Chart.defaults.global.defaultFontColor = colors.muted;

            var weeklyCanvas = document.getElementById('funnel-weekly-chart');
            var weeklyDatasets = (weeklyChart.datasets || []).map(function (dataset) {
                var palette = stepPalette[dataset.name] || stepPalette.route_filled;
                return {
                    label: dataset.label,
                    data: dataset.values || [],
                    borderColor: palette.line,
                    backgroundColor: dataset.name === 'completed' ? areaGradient(weeklyCanvas, palette.fill) : palette.fill,
                    borderWidth: dataset.name === 'completed' ? 3.5 : 2.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: palette.line,
                    pointBorderColor: colors.white,
                    pointBorderWidth: 2,
                    fill: dataset.name === 'completed',
                    lineTension: 0.28
                };
            });

            createChart('funnel-weekly-chart', {
                type: 'line',
                data: {
                    labels: weeklyChart.labels || [],
                    datasets: weeklyDatasets
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {display: false},
                    tooltips: Object.assign(tooltipOptions(), {
                        callbacks: {
                            label: function (item, data) {
                                return data.datasets[item.datasetIndex].label + ': ' + item.yLabel + '%';
                            },
                            afterBody: function (items) {
                                var index = items.length ? items[0].index : 0;
                                var started = (weeklyChart.started || [])[index] || 0;
                                var completed = (weeklyChart.completed || [])[index] || 0;
                                return [
                                    'Начали: ' + numberFormat.format(started),
                                    'Завершили: ' + numberFormat.format(completed)
                                ];
                            }
                        }
                    }),
                    scales: {
                        xAxes: [{
                            gridLines: {display: false},
                            ticks: {fontColor: colors.muted, fontSize: 11}
                        }],
                        yAxes: [{
                            gridLines: {
                                color: colors.grid,
                                zeroLineColor: colors.grid,
                                borderDash: [4, 4]
                            },
                            ticks: {
                                fontColor: colors.muted,
                                fontSize: 11,
                                callback: function (value) {
                                    return value + '%';
                                }
                            }
                        }]
                    }
                }
            });

            createChart('funnel-dropoff-chart', {
                type: 'doughnut',
                data: {
                    labels: dropoffChart.labels || [],
                    datasets: [{
                        data: dropoffChart.values || [],
                        backgroundColor: [colors.coral, colors.orange, colors.indigo, colors.teal],
                        borderColor: '#ffffff',
                        borderWidth: 6,
                        hoverBorderColor: '#ffffff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutoutPercentage: 68,
                    legend: {display: false},
                    tooltips: Object.assign(tooltipOptions(), {
                        callbacks: {
                            label: function (item, data) {
                                var share = (dropoffChart.shares || [])[item.index] || 0;
                                return data.labels[item.index] + ': ' + numberFormat.format(data.datasets[0].data[item.index]) + ' (' + share + '%)';
                            }
                        }
                    })
                }
            });

            var timeCanvas = document.getElementById('funnel-time-chart');
            createChart('funnel-time-chart', {
                type: 'line',
                data: {
                    labels: timeChart.labels || [],
                    datasets: [{
                        label: 'Среднее',
                        data: timeChart.average_minutes || [],
                        borderColor: colors.blue,
                        backgroundColor: areaGradient(timeCanvas, 'rgba(47, 123, 255, 0.14)'),
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.blue,
                        pointBorderColor: colors.white,
                        pointBorderWidth: 2,
                        fill: true,
                        lineTension: 0.28
                    }, {
                        label: 'Медиана',
                        data: timeChart.median_minutes || [],
                        borderColor: colors.orange,
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: colors.orange,
                        pointBorderColor: colors.white,
                        pointBorderWidth: 2,
                        fill: false,
                        lineTension: 0.25
                    }, {
                        label: timeChart.reference_label || 'Ориентир периода',
                        data: timeChart.reference_series || [],
                        borderColor: colors.teal,
                        borderWidth: 2,
                        borderDash: [3, 5],
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        fill: false,
                        lineTension: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {display: false},
                    tooltips: Object.assign(tooltipOptions(), {
                        callbacks: {
                            label: function (item, data) {
                                return data.datasets[item.datasetIndex].label + ': ' + item.yLabel + ' мин';
                            }
                        }
                    }),
                    scales: {
                        xAxes: [{
                            gridLines: {display: false},
                            ticks: {fontColor: colors.muted, fontSize: 11}
                        }],
                        yAxes: [{
                            gridLines: {
                                color: colors.grid,
                                zeroLineColor: colors.grid,
                                borderDash: [4, 4]
                            },
                            ticks: {
                                fontColor: colors.muted,
                                fontSize: 11,
                                callback: function (value) {
                                    return value + ' мин';
                                }
                            }
                        }]
                    }
                }
            });

            createChart('funnel-stuck-gauge', {
                type: 'doughnut',
                data: {
                    labels: stuckGauge.labels || [],
                    datasets: [{
                        data: stuckGauge.values || [],
                        backgroundColor: ['#ff8b66', '#d9e6f0'],
                        borderColor: '#f7fbff',
                        borderWidth: 4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    rotation: Math.PI,
                    circumference: Math.PI,
                    cutoutPercentage: 74,
                    legend: {display: false},
                    tooltips: Object.assign(tooltipOptions(), {
                        callbacks: {
                            label: function (item, data) {
                                return data.labels[item.index] + ': ' + numberFormat.format(data.datasets[0].data[item.index]);
                            }
                        }
                    })
                }
            });
        })();
    </script>
@endpush
