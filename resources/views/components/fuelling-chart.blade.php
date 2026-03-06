@props([
    'carId' => null,
])

<div
    class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-md dark:shadow-gray-900/50 p-6"
    wire:ignore
    x-data="fuellingChart({
        carId: @js($carId),
        endpointTemplate: @js(route('graph.fuelling', ['carId' => '__CAR_ID__'])),
    })"
    x-init="init()"
    x-on:fuelling-chart-refresh.window="refresh($event.detail)"
>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Abastecimentos - ultimos 30 dias</h3>
    </div>

    <div class="h-80">
        <canvas x-ref="canvas"></canvas>
    </div>

    <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600"></p>
</div>

@once
    <script>
        window.__chartJsLoader = window.__chartJsLoader || new Promise((resolve, reject) => {
            if (window.Chart) {
                resolve(window.Chart);
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = () => resolve(window.Chart);
            script.onerror = () => reject(new Error('Falha ao carregar o Chart.js.'));
            document.head.appendChild(script);
        });

        function fuellingChart(config) {
            return {
                chart: null,
                carId: config.carId,
                endpointTemplate: config.endpointTemplate,
                error: '',
                requestId: 0,
                init() {
                    this.$nextTick(() => this.renderForCar(this.carId));
                },
                refresh(detail) {
                    const carId = this.extractCarId(detail);
                    if (!carId) {
                        return;
                    }

                    this.carId = carId;
                    this.$nextTick(() => this.renderForCar(carId));
                },
                extractCarId(detail) {
                    if (typeof detail === 'number' || typeof detail === 'string') {
                        const parsed = Number(detail);
                        return Number.isFinite(parsed) && parsed > 0 ? parsed : null;
                    }

                    if (detail && typeof detail === 'object') {
                        if ('carId' in detail) {
                            const parsed = Number(detail.carId);
                            return Number.isFinite(parsed) && parsed > 0 ? parsed : null;
                        }

                        if (Array.isArray(detail) && detail.length > 0 && detail[0]?.carId) {
                            const parsed = Number(detail[0].carId);
                            return Number.isFinite(parsed) && parsed > 0 ? parsed : null;
                        }
                    }

                    return null;
                },
                async renderForCar(carId) {
                    if (!carId) {
                        this.error = 'Selecione um veiculo para visualizar o grafico.';
                        this.destroyChart();
                        return;
                    }

                    this.error = '';
                    const currentRequestId = ++this.requestId;

                    try {
                        const endpoint = this.endpointTemplate.replace('__CAR_ID__', String(carId));
                        const response = await fetch(endpoint, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Nao foi possivel carregar os dados do grafico.');
                        }

                        const payload = await response.json();
                        await window.__chartJsLoader;

                        if (currentRequestId !== this.requestId) {
                            return;
                        }

                        this.drawChart(payload);
                    } catch (error) {
                        if (currentRequestId !== this.requestId) {
                            return;
                        }

                        this.destroyChart();
                        this.error = error.message;
                    }
                },
                drawChart(payload) {
                    if (!payload?.data?.labels || !payload?.data?.datasets) {
                        this.destroyChart();
                        this.error = 'Formato de dados invalido para o Chart.js.';
                        return;
                    }

                    const canvas = this.$refs.canvas;
                    if (!canvas || typeof canvas.getContext !== 'function') {
                        this.destroyChart();
                        this.error = 'Canvas do grafico nao esta disponivel.';
                        return;
                    }

                    const context = canvas.getContext('2d');
                    if (!context) {
                        this.destroyChart();
                        this.error = 'Contexto do grafico nao esta disponivel.';
                        return;
                    }

                    this.destroyChart();

                    this.chart = new Chart(context, {
                        type: payload.type ?? 'line',
                        data: payload.data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback(value) {
                                            return 'R$ ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                destroyChart() {
                    if (this.chart) {
                        this.chart.destroy();
                        this.chart = null;
                    }
                }
            };
        }
    </script>
@endonce
