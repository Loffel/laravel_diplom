<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Динамика цен для продукта {{ product.title }}</h3>
                            <div class="card-tools">
                                <b v-if="product.shop.currency != 'RUB'">Курс: 1$={{rubRate}}₽</b>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <highcharts :constructor-type="'stockChart'" :options="chartOptions"></highcharts>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Последние 30 значений цены</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Реальные данные</th>
                                        <th>Прогноз по МНК (Отн. ошибка - {{ errorMNK.toFixed(2) }}%)</th>
                                        <th>Прогноз по скользящей средней (Отн. ошибка - {{ errorAvr.toFixed(2) }}%)</th>
                                    </tr>
                                </thead>
                                <tbody v-if="product.shop.currency != 'RUB'">
                                    <tr v-for="n in lastApproxPrices.length" v-bind:key="n">
                                        <td>{{ lastApproxPrices[lastApproxPrices.length - n][0] | priceDate }}</td>
                                        <td>{{ compareDates(lastApproxPrices[lastApproxPrices.length - n][0]) == 0 ? '-':compareDates(lastApproxPrices[lastApproxPrices.length - n][0]) }}<span v-if="compareDates(lastApproxPrices[lastApproxPrices.length - n][0])" class="text-muted">$ ({{ (compareDates(lastApproxPrices[lastApproxPrices.length - n][0]) * rubRate).toFixed(2) }}₽)</span></td>
                                        <td>{{ lastApproxPrices[lastApproxPrices.length - n][1].toFixed(2) }}<span class="text-muted">$ ({{ (lastApproxPrices[lastApproxPrices.length - n][1] * rubRate).toFixed(2) }}₽)</span></td>
                                        <td>{{ lastAvrPrices[lastApproxPrices.length - n][1].toFixed(2) }}<span class="text-muted">$ ({{ (lastAvrPrices[lastApproxPrices.length - n][1] * rubRate).toFixed(2) }}₽)</span></td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-for="n in lastApproxPrices.length" v-bind:key="n">
                                        <td>{{ lastApproxPrices[lastApproxPrices.length - n][0] | priceDate }}</td>
                                        <td>{{ compareDates(lastApproxPrices[lastApproxPrices.length - n][0]) == 0 ? '-':compareDates(lastApproxPrices[lastApproxPrices.length - n][0]) + '₽' }}</td>
                                        <td>{{ lastApproxPrices[lastApproxPrices.length - n][1].toFixed(2) }}₽</td>
                                        <td>{{ lastAvrPrices[lastApproxPrices.length - n][1].toFixed(2) }}₽</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
</template>

<script>
    export default {
        data() {
            return {
                rubRate: 0,
                pricesReal: [],
                pricesApprox: [],
                pricesAvr: [],
                pricesAvr2: [],
                lastRealPrices: [],
                lastApproxPrices: [],
                lastAvrPrices: [],
                errorMNK: 0,
                errorAvr: 0,
                chartOptions: {
                    legend: {
                        enabled: true
                    },
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            format: '{value:%Y-%m-%d}',
                            rotation: 45,
                            align: 'left'
                        },
                    },
                    series: [
                        
                    ],
                    rangeSelector: {
                        buttons: [
                            {
                                type: 'month',
                                count: 1,
                                text: '1 мес'
                            },{
                                type: 'month',
                                count: 2,
                                text: '2 мес'
                            }, {
                                type: 'month',
                                count: 3,
                                text: '3 мес'
                            },{
                                type: 'month',
                                count: 6,
                                text: '6 мес'
                            },{
                                type: 'year',
                                count: 1,
                                text: '1 год'
                            },{
                                type: 'all',
                                text: 'Все'
                            }
                        ]
                    },
                    marker: {
                        enabled: true,
                        radius: 5
                    }
                },
                product: {}
            }
        },
        methods: {
            loadRate(){
                axios.get('/api/product/exchange').then((data) => {
                    this.rubRate = data.data.rates.RUB.toFixed(2);
                }).catch((e) => {
                    console.log(e);
                });
            },
            compareDates(date){
                for(let i = 0; i < this.pricesReal.length; i++){
                    if(this.pricesReal[i][0] == date) {
                        return this.pricesReal[i][1].toFixed(2);
                    }
                };

                return 0;
            },
            loadChart(){
                axios.get(`/api/product/${this.$route.params.id}`).then(( data ) => {
                    this.product = data.data;

                    this.product.prices.forEach(element => {
                        let dateArray = [+this.$options.filters.epochDate(element.date) * 1000,
                                parseFloat(element.value)];

                        if(element.type == "real")
                            this.pricesReal.push(dateArray);
                        else if(element.type == "predict") this.pricesApprox.push(dateArray);
                        else if(element.type == "predict_avr") this.pricesAvr.push(dateArray);
                    });

                    let howManyPrices = this.pricesApprox.length >= 30 ? 30 : this.pricesApprox.length;

                    this.lastRealPrices = this.pricesReal.slice(-howManyPrices);
                    this.lastApproxPrices = this.pricesApprox.slice(-howManyPrices);
                    this.lastAvrPrices = this.pricesAvr.slice(-howManyPrices);

                    this.errorMNK = 0;
                    this.errorAvr = 0;

                    for(let i = 0; i < this.pricesApprox.length - 7; i++){
                        // console.log(this.compareDates(this.pricesApprox[i][0]));
                        let realPrice = this.compareDates(this.pricesApprox[i][0]);
                        this.errorMNK += Math.abs((realPrice - this.pricesApprox[i][1])/realPrice) * 100;
                        this.errorAvr += Math.abs((realPrice - this.pricesAvr[i][1])/realPrice) * 100;
                    }

                    this.errorMNK = this.errorMNK / (this.pricesApprox.length - 7);
                    this.errorAvr = this.errorAvr / (this.pricesAvr.length - 7);


                    this.pricesAvr2 = this.pricesAvr.slice(-7);
                    this.pricesAvr = this.pricesAvr.slice(0, this.pricesAvr.length - 7);

                    // if(this.pricesReal.length > 0) this.pricesApprox.unshift(this.pricesReal[this.pricesReal.length - 1])

                    this.chartOptions.series = [
                        {
                            name: 'Реальные значения',
                            data: this.pricesReal
                        },
                        {
                            name: 'Прогноз',
                            data: this.pricesApprox
                        },
                        {
                            name: 'Скользящая средняя',
                            data: this.pricesAvr,
                            id: 'avr',
                            color: '#90ed7d'
                        },
                        {
                            data: this.pricesAvr2,
                            linkedTo: 'avr',
                            color: '#90ed7d'
                        }
                    ];
                });
            }
        },
        beforeMount() {
            this.loadRate();
            this.loadChart();
        }
    }

</script>
