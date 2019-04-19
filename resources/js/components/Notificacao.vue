<template>
    <div></div>
</template>

<script>
    export default {
        mounted() {
            const self = this;
            if (this.mensagem) {
                this.notificar(this.mensagem, this.tipo ? this.tipo : 'alert');
            }
            this.$root.$on('notificar', (mensagem, tipo, layout) => {
                self.notificar(mensagem, tipo, layout);
            });

        },
        props: {
            mensagem: String,
            tipo: String
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
                    timeout: 10000,
                    text: this.n_mensagem
                }).show();
            },
            notificar: function (mensagem, tipo, layout) {
                this.n_mensagem = mensagem;
                this.n_tipo = tipo ? tipo : 'warning';
                this.n_layout = layout ? layout : 'topCenter';
                this.showNotification();
            }
        }
    }
</script>
