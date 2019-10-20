<template>
    <div>
        <vue-topprogress ref="topProgress"></vue-topprogress>
        <div class="row">
            <div class="col-md-12"  v-if="statusMusicaAtual" v-bind:class="{ 'col-lg-6': proximasMusicas.length > 0, 'col-lg-12': proximasMusicas.length === 0 }">
                <div class="text-center">
                    <img v-if="imgMusicaAtual !== ' '" :src="imgMusicaAtual" :alt="musicaAtual" class="img-fluid item-image"/>
                    <h1 class="musicaEmReproducao">{{ musicaAtual }}</h1>
                    <small class="autorMusicaReproducao text-muted">{{ musicaAtualAutor }}</small>
                    <!--<a href="#" @click="pularMusica()"><p><small>pular</small></p></a>-->
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <small class="text-muted cat">
                            <span class="d-block p-0"><i class="fas fa-user-alt text-secondary"></i>  Por {{ objMusicaAtual.autor }}</span>
                            <span class="d-block p-0"><i class="fas fa-play text-secondary"></i>  {{ objMusicaAtual.plays }} plays</span>
                            <span class="d-block p-0"><i class="fas fa-heart text-secondary"></i>  {{ objMusicaAtual.likes }} favs</span>
                        </small>
                    </div>
                    <div class="col-md-4">
                        <span v-if="!objMusicaAtual.liked" v-on:click="likeMusica" class="float-right align-middle d-block p-0"><i class="fas fa-heart fa-3x text-secondary"></i></span>
                        <span v-if="objMusicaAtual.liked" v-on:click="likeMusica" class="float-right align-middle d-block p-0"><i class="fas fa-heart fa-3x text-danger"></i></span>
                    </div>
                </div>
            </div>
            <div v-bind:class="{ 'col-lg-6 col-md-12': statusMusicaAtual, 'col-lg-12': !statusMusicaAtual }" class="text-white">
                <div class="row">
                    <div class="col-sm-12">
                        <div v-if="proximasMusicas.length > 0">
                            <small>next: {{ proximaMusicaAutor }}</small>
                            <h2>{{ proximaMusica }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table text-white">
                                <tbody>
                                <tr v-for="item in proximasMusicas">
                                    <td> {{ item.name }} </td>
                                    <td> {{ item.username }} </td>
                                    <td v-on:click="removerMusicaFila(item.id)" class="align-middle"><button type="button" class="btn btn-link"><small><i class="material-icons text-danger">close</i></small></button></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="text-white">
            <h3>Buscar música</h3>

            <div class="input-group mb-3">
                <input v-model="buscaMusica" type="text" name="musica" class="form-control input" placeholder="Digite a busca"
                       v-on:keyup.enter="pesquisarMusica" autocomplete="off" aria-label="Busca">
                <div class="input-group-append">
                    <button class="btn btn-outline-light" id="button-addon2" v-on:click="pesquisarMusica" >Buscar</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table text-white" v-if="retornoMusicasBuscadas.length > 0">
                    <thead>
                    <tr>
                        <th> </th>
                        <th> artista</th>
                        <th> música</th>
                        <th class="text-center"> ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in retornoMusicasBuscadas">
                        <td class="align-middle text-center">  <img :src="item.imageurl" :alt="item.name" /></td>
                        <td class="align-middle"> {{ item.artistsname }}</td>
                        <td class="align-middle"> {{ item.name }} </td>
                        <td class="align-middle text-center" v-on:click="adicionarMusica(item.uri, item.desc)"><button type="button" class="btn btn-link"><i class="material-icons text-success">add</i></button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import { vueTopprogress } from 'vue-top-progress'

    export default {
        mounted() {
            const self = this;

            self.$root.$on('atualizar-componente', (id) => {
                self.idFilaAtual = id;
                self.retornoMusicasBuscadas = [];
                self.buscaMusica = "";
                self.atualizarComponentes();
            });

            Echo.channel('filas')
                .listen('MusicaAdicionada', (e) => {
                    if (e.musica.id_fila === self.idFilaAtual) {
                        self.getProximasMusicas();
                    }
                }).listen('MusicaFinalizada', (e) => {
                if (e.item.id_fila === self.idFilaAtual) {
                    self.getMusicaAtual();
                    self.getProximasMusicas();
                }
            }).listen('MusicaIniciada', (e) => {
                if (e.item.id_fila === self.idFilaAtual) {
                    self.getMusicaAtual();
                    self.getProximasMusicas();
                }
            }).listen('MusicaRemovida', (e) => {
                self.getProximasMusicas();
                self.getProximasMusicas();
            });

            /*setTimeout(function () {
                location.reload();
            }, 600000);*/

            setInterval(function () {
                if (self.idFilaAtual) {
                    self.getMusicaAtual();
                }
            }, 3000)
        },
        components: {
            vueTopprogress
        },
        data() {
            return {
                proximasMusicas: [],
                proximaMusica: ' ',
                proximaMusicaAutor: ' ',
                imgMusicaAtual: ' ',
                musicaAtual: ' ',
                objMusicaAtual: {},
                musicaAtualAutor: ' ',
                n_layout: 'topCenter',
                status: 0,
                total: 60,
                buscaMusica: "",
                retornoMusicasBuscadas: [],
                idFilaAtual: this.idFila,
                statusMusicaAtual: false,
            }
        },
        props: {
            idFila: String,
        },
        methods: {
            atualizarComponentes() {
                this.getProximasMusicas();
                this.getMusicaAtual();
                this.getUserData();
            },
            getProximasMusicas() {
                const self = this;
                //this.$refs.topProgress.start();
                axios.get('/api/getproximasmusicas/'+self.idFilaAtual)
                    .then(res => {
                        const musicas = res.data.data;

                        self.proximasMusicas = [];

                        musicas.forEach((musica) => {

                            self.proximasMusicas.push(musica);
                        });
                        if (musicas[0]) {
                            self.proximaMusica = musicas[0].name;
                            self.proximaMusicaAutor = "por " + musicas[0].username;
                        } else {
                            self.proximaMusica = "Aleatória!";
                        }
                        //this.$refs.topProgress.done();

                    }).catch(err => {
                    //self.$refs.topProgress.fail();
                    //this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                    console.log(err);
                })
            },
            getMusicaAtual() {
                const self = this;
                //this.$refs.topProgress.start();
                axios.get('/api/getmusicaatual/'+self.idFilaAtual)
                    .then(res => {
                        self.musicaAtual = res.data.data;
                        self.musicaAtualAutor = res.data.autor;
                        self.imgMusicaAtual = res.data.image;
                        self.statusMusicaAtual = res.data.status;
                        self.objMusicaAtual = res.data;

                        //this.$refs.topProgress.done();

                    }).catch(err => {
                    //self.$refs.topProgress.fail();
                    //this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                    console.log(err);
                })
            },
            alterarStatus() {
                const self = this;
                this.$refs.topProgress.start();
                axios.get('/api/trocarstatus')
                    .then(res => {
                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', res.data.message, 'success');
                        }
                        this.$refs.topProgress.done();

                        this.getUserData();
                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                    console.log(err);
                })
            },
            getUserData() {
                const self = this;
                this.$refs.topProgress.start();
                axios.get('/api/getuserdata')
                    .then(res => {
                        self.status = res.data.data.status;
                        this.$refs.topProgress.done();

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                    console.log(err);
                });
            },
            pesquisarMusica() {
                const self = this;
                this.$refs.topProgress.start();

                if (self.buscaMusica && self.buscaMusica !== "") {
                    axios.post('/api/buscarmusica', {busca: self.buscaMusica,})
                        .then(res => {
                            const musicasEncontradas = res.data.data;
                            self.retornoMusicasBuscadas = [];

                            musicasEncontradas.forEach((musica) => {
                                const imageurl = musica.album.images[2].url;

                                let artistas = "";
                                const desc = musica.artists[0].name + " - " + musica.name;

                                musica.artists.forEach((artista)=> {
                                    artistas += artista.name+" ";
                                });

                                const obj = {
                                    imageurl: imageurl,
                                    artistsname: artistas,
                                    desc: desc,
                                    name: musica.name,
                                    uri: musica.uri
                                };
                                self.retornoMusicasBuscadas.push(obj);
                            });


                            this.$refs.topProgress.done();

                        }).catch(err => {
                        self.$refs.topProgress.fail();
                        this.$refs.topProgress.done();
                        self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                        console.log(err);
                    });
                } else {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Digite uma busca.', 'error');
                }

            },
            adicionarMusica(uri, desc) {

                const self = this;

                self.$refs.topProgress.start();

                axios.post('/api/adicionarmusica', {uri: uri, desc: desc, id_fila: self.idFilaAtual})
                    .then(res => {

                        self.$refs.topProgress.done();

                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', 'Música adicionada com sucesso. ', 'success');
                        }

                        self.getProximasMusicas();

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    self.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao adicionar a música. ', 'error');
                    console.log(err);
                });
            },
            removerMusicaFila(id) {
                const self = this;

                self.$refs.topProgress.start();

                axios.post('/api/removermusica', {id: id})
                    .then(res => {

                        self.$refs.topProgress.done();

                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', 'Música removida com sucesso. ', 'success');
                        }

                        self.getProximasMusicas();

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    self.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao remover a música. ', 'error');
                    console.log(err);
                });
            },
            pularMusica(){
                const self = this;

                self.$refs.topProgress.start();

                axios.post('/api/proximamusica', {idFila: self.idFilaAtual})
                    .then(res => {

                        self.$refs.topProgress.done();

                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', 'É pra já!', 'success');
                        }

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    self.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao passar a música. ', 'error');
                    console.log(err);
                });
            },
            likeMusica() {
                const self = this;
                this.$refs.topProgress.start();
                self.objMusicaAtual.liked = !self.objMusicaAtual.liked;

                axios.post('/api/like/'+self.objMusicaAtual.id)
                    .then(res => {
                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.getMusicaAtual();
                        }
                        this.$refs.topProgress.done();

                    }).catch(err => {
                    console.log(err);
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao dar like na música. ', 'error');
                })

            },
        }
    }
</script>

<style scoped>
    .musicaEmReproducao {
        font-family: 'Fredoka One', cursive;
        color: white;
    }
    .autorMusicaReproducao {
        color: white;
    }
    .input {
        color: white;
        background-color:transparent;
    }
    input:focus,
    select:focus,
    textarea:focus,
    button:focus {
        outline: none;
    }

    .item-image {
        box-shadow: 11px 11px 11px rgba(0, 0, 33,.2);
    }

    .item-image:hover {
        -moz-transform: scale(1.1);
        -webkit-transform: scale(1.1);
        transform: scale(1.05);
        box-shadow: 11px 11px 11px rgba(33,33,33,.2);
    }

</style>
