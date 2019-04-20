<template>
    <div>
        <vue-topprogress ref="topProgress"></vue-topprogress>
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="text-center">
                    <img v-if="imgMusicaAtual !== ' '" :src="imgMusicaAtual" :alt="musicaAtual" />
                    <h1 class="musicaEmReproducao">{{ musicaAtual }}</h1>
                    <small class="autorMusicaReproducao">{{ musicaAtualAutor }}</small>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 text-white">
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
                                    <td v-on:click="removerMusicaFila(item.id)"> <i class="material-icons">close</i> </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-white">
            <h3>Buscar música</h3>

            <div class="input-group mb-3">
                <input v-model="buscaMusica" type="text" name="musica" class="form-control input" placeholder="Digite a busca"
                       v-on:keyup.enter="pesquisarMusica" autocomplete="off" aria-label="Busca">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" id="button-addon2" v-on:click="pesquisarMusica" >Buscar</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table text-white" v-if="retornoMusicasBuscadas.length > 0">
                    <thead>
                    <tr>
                        <th> </th>
                        <th> música</th>
                        <th> artista</th>
                        <th class="text-center"> ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in retornoMusicasBuscadas">
                        <td class="align-middle text-center">  <img :src="item.imageurl" :alt="item.name" /></td>
                        <td class="align-middle"> {{ item.artistsname }}</td>
                        <td class="align-middle"> {{ item.name }} </td>
                        <td class="align-middle text-center" v-on:click="adicionarMusica(item.uri, item.desc)"><i class="material-icons">add</i></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <button type="button" class="btn btn-danger btn-sm" v-on:click="alterarStatus()" v-if="status === true">
                    Desativar
                </button>
                <button type="button" class="btn btn-success btn-sm" v-on:click="alterarStatus()" v-if="status === false">
                    Ativar
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { vueTopprogress } from 'vue-top-progress'

    export default {
        mounted() {
            this.getProximasMusicas();
            this.getMusicaAtual();
            this.getUserData();

            const self = this;
            Echo.channel('filas')
                .listen('MusicaAdicionada', (e) => {
                    self.getProximasMusicas();
                }).listen('MusicaFinalizada', (e) => {
                self.getMusicaAtual();
                self.getProximasMusicas();
            }).listen('MusicaIniciada', (e) => {
                self.getMusicaAtual();
                self.getProximasMusicas();
            }).listen('MusicaRemovida', (e) => {
                self.getProximasMusicas();
            });

            setTimeout(function () {
                location.reload();
            }, 600000);
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
                musicaAtualAutor: ' ',
                n_layout: 'topCenter',
                status: 0,
                total: 60,
                buscaMusica: "",
                retornoMusicasBuscadas: [],
            }
        },
        methods: {
            getProximasMusicas() {
                const self = this;
                this.$refs.topProgress.start();
                axios.get('/api/getproximasmusicas')
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
                        this.$refs.topProgress.done();

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                    console.log(err);
                })
            },
            getMusicaAtual() {
                const self = this;
                this.$refs.topProgress.start();
                axios.get('/api/getmusicaatual')
                    .then(res => {
                        self.musicaAtual = res.data.data;
                        self.musicaAtualAutor = res.data.autor;
                        self.imgMusicaAtual = res.data.image;

                        this.$refs.topProgress.done();

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
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

                axios.post('/api/adicionarmusica', {uri: uri, desc: desc})
                    .then(res => {

                        self.$refs.topProgress.done();

                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.$root.$emit('notificar', 'Música adicionada com sucesso. ', 'success');
                        }

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

                    }).catch(err => {
                    self.$refs.topProgress.fail();
                    self.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao remover a música. ', 'error');
                    console.log(err);
                });
            }
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

</style>
