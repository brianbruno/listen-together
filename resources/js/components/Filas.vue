<template>
    <div class="container">
        <vue-topprogress ref="topProgress"></vue-topprogress>

        <div class="row justify-content-center">
            <div class="col-md-6" v-for="fila in filas">
                <div>
                    <figure class="snip0051">
                        <img :src="fila.capa_fila" alt="sample1"/>
                        <div class="icons">
                            <a href="#" @click="trocarFila(fila.id)"><i class="fas fa-headphones-alt"></i></a>
                            <a href="#" @click="votarFila(fila.id)"><i class="far fa-thumbs-up"></i></a>
                            <a href="#" @click="abrirModal(fila.id)" data-toggle="modal" data-target="#exampleModalLong"><i class="fas fa-info"></i></a>
                        </div>
                        <figcaption>
                            <h2>{{ fila.username }} <span>{{ fila.name }}</span></h2>
                            <p>{{ fila.descricao }}</p>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content background-modal">
                    <!--<div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Em reprodução: </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>-->
                    <div class="modal-body">
                        <reprodutor :idFila="idFilaClick"></reprodutor>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal">fechar</button>
                        </div>
                    </div>
                   <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { vueTopprogress } from 'vue-top-progress'

    export default {
        mounted() {
            $("figure").mouseleave(
                function () {
                    $(this).removeClass("hover");
                }
            );

            const self = this;

            setInterval(function () {
                self.getFilas();
            }, 10000);

            this.getFilas()
        },
        components: {
            vueTopprogress
        },
        data() {
            return {
                filas: [],
                idFilaClick: 0,
            }
        },
        methods: {
            getFilas() {
                const self = this;
                self.$refs.topProgress.start();
                axios.get('/api/filas')
                    .then(res => {

                        if (res.data.status) {
                            const filas = res.data.data;

                            self.filas = [];

                            filas.forEach((fila) => {
                                self.filas.push(fila);
                            });

                            this.$refs.topProgress.done();

                        } else {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        }


                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar as filas. ', 'error');
                    console.log(err);
                })
            },
            trocarFila(id) {
                const self = this;
                this.$refs.topProgress.start();
                axios.post('/api/trocarfila', {fila: id,})
                    .then(res => {
                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', res.data.message, 'success');
                        }
                        this.$refs.topProgress.done();

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao alterar a fila. ', 'error');
                    console.log(err);
                })
            },
            abrirModal(id) {
                this.idFilaClick = id;
                this.$root.$emit('atualizar-componente', id);
            },
            votarFila(id) {
                const self = this;
                this.$refs.topProgress.start();
                axios.post('/api/votar/'+id)
                    .then(res => {
                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', res.data.message, 'success');
                        }
                        self.$refs.topProgress.done();

                        self.getFilas();
                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao votar na fila. ', 'error');
                    console.log(err);
                })
            },
        }
    }
</script>

<style scoped>
    @import url(https://fonts.googleapis.com/css?family=Raleway:400,200,300,800);
    .background-modal {
        background-color: #1d2124;
    }

    figure.snip0051 {
        font-family: 'Raleway', Arial, sans-serif;
        position: relative;
        float: left;
        overflow: hidden;
        margin: 10px 1%;
        min-width: 220px;
        max-width: 480px;
        max-height: 240px;
        width: 100%;
        background: #000000;
    }
    figure.snip0051 * {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }
    figure.snip0051 > img {
        margin-left: 50%;
        opacity: 1;
        width: 50%;
        filter: blur(0px);
        -webkit-transition: all 0.35s;
        transition: all 0.35s;
    }
    figure.snip0051 figcaption {
        left: 0;
        position: absolute;
        top: 0;
        width: 50%;
        height: 100%;
        background: #ffffff;
    }
    figure.snip0051 figcaption h2,
    figure.snip0051 figcaption p {
        margin: 0;
        color: #000000;
        text-align: right;
        position: absolute;
        padding: 10px 0 10px 0px;
        margin: 0 40px 0 20px;
    }
    figure.snip0051 figcaption h2 {
        font-size: 1.3em;
        bottom: 50%;
        right: 0;
        font-weight: 300;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    }
    figure.snip0051 figcaption h2 span {
        font-weight: 800;
    }
    figure.snip0051 figcaption p {
        top: 50%;
        font-size: 0.9em;
        opacity: 0.8;
    }
    figure.snip0051 .icons {
        padding: 5px;
        position: absolute;
        left: 50%;
        top: 50%;
        padding-left: 30px;
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
    }
    figure.snip0051 .icons i {
        font-size: 32px;
        padding: 10px;
        color: #ffffff;
        opacity: 0;
        top: 50%;
        display: inline-block;
        -webkit-transition: all 0.35s;
        transition: all 0.35s;
        -webkit-transform: translateX(50px);
        transform: translateX(50px);
    }
    figure.snip0051 .icons a:first-child i {
        -webkit-transition-delay: 0.2s;
        transition-delay: 0.2s;
    }
    figure.snip0051 .icons a:nth-child(2) i {
        -webkit-transition-delay: 0.1s;
        transition-delay: 0.1s;
    }
    figure.snip0051 .icons a:nth-child(3) i {
        -webkit-transition-delay: 0s;
        transition-delay: 0s;
    }
    figure.snip0051:after {
        position: absolute;
        top: 50%;
        left: 50%;
        height: 35px;
        width: 35px;
        background-color: #000000;
        content: '';
        -webkit-transform: rotate(45deg) translate(-50%, -50%);
        transform: rotate(45deg) translate(-50%, -50%);
        -webkit-transform-origin: 0 0;
        transform-origin: 0 0;
    }
    figure.snip0051:hover > img,
    figure.snip0051.hover > img {
        opacity: 0.5;
        filter: blur(5px);
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
    figure.snip0051:hover i,
    figure.snip0051.hover i {
        opacity: 0.8;
        -webkit-transform: translate(0);
        transform: translate(0);
    }
    figure.snip0051:hover a:first-child i,
    figure.snip0051.hover a:first-child i {
        -webkit-transition-delay: 0.1s;
        transition-delay: 0.1s;
    }
    figure.snip0051:hover a:nth-child(2) i,
    figure.snip0051.hover a:nth-child(2) i {
        -webkit-transition-delay: 0.2s;
        transition-delay: 0.2s;
    }
    figure.snip0051:hover a:nth-child(3) i,
    figure.snip0051.hover a:nth-child(3) i {
        -webkit-transition-delay: 0.3s;
        transition-delay: 0.3s;
    }
    /* Demo purposes only */
    body {
        background-color: #212121;
    }
</style>