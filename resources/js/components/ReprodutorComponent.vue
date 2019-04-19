<template>
    <div class="card">
        <div class="card-header">Ouvir Juntos!</div>

        <div class="card-body">
            Música atual: teste teste teste
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            Echo.private('fila.default')
                .listen('MusicaAdicionada', (e) => {
                    console.log(e.order.name);
                }).listen('MusicaFinalizada', (e) => {
                console.log(e.order.name);
                }).listen('MusicaIniciada', (e) => {
                    console.log(e.order.name);
                });
        },
        data() {
            return {
                n_mensagem: String,
                n_tipo: String,
                n_layout: 'topCenter'
            }
        },
        methods: {
            showNotification: function () {
                new Noty({
                    theme: 'mint',
                    type: this.n_tipo,
                    layout: this.n_layout,
                    progressBar: true,
                    timeout: 5000,
                    text: this.n_mensagem
                }).show();
            },
            notificar: function (mensagem, tipo, layout) {
                this.n_mensagem = mensagem;
                this.n_tipo = tipo;
                this.n_layout = layout ? layout : 'topCenter'
                this.showNotification();
            }
        }
    }
</script>
