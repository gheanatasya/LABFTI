<template>
    <div class="" style="position: fixed; top: 0; width: 100%;">
        <v-toolbar style="background-color: rgb(2,39,10,0.9); font-family: 'Lexend-Regular'; ">
            <v-toolbar-title>
                <router-link to="/" style="cursor: pointer">
                    <v-img :width="80" cover src="../picture/fti-ukdw.png"></v-img>
                </router-link>
            </v-toolbar-title>
            <v-toolbar-items class="flex-grow-1 justify-center" style="color: white">
                <v-btn flat v-for="menu in menusCenter" :key="menu.title" :to="menu.to">
                    {{ menu.title }}
                </v-btn>
            </v-toolbar-items>
            <v-spacer></v-spacer>
            <v-toolbar-items class="hidden-xs-only" style="color: white">
                <template v-for="menu1 in menusLeft" :key="menu1.title">
                    <v-menu v-if="menu1.title === 'Pengaturan'">
                        <template v-slot:activator="{ on, props }">
                            <v-btn flat v-on="on" v-bind="props">
                                <v-icon left dark>{{ menu1.icon }}</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item v-for="(item, i) in menu1.submenus" :key="i">
                                <v-list-item-title>
                                    <v-icon>{{ item.icon }}</v-icon>
                                    <v-btn flat @click="logout" v-if="item.title === 'Logout'">
                                        {{ item.title }}
                                    </v-btn>
                                    <v-btn :to="item.to" v-else flat>
                                        {{ item.title }}
                                    </v-btn>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <v-menu v-if="menu1.title === 'Pemberitahuan'">
                        <template v-slot:activator="{ on, props }">
                            <v-btn flat v-on="on" v-bind="props">
                                <v-badge :content="this.allNotifications.length">
                                    <v-icon left dark size="x-large">{{ menu1.icon }}</v-icon></v-badge>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item v-for="(item, i) in this.allNotifications" :key="i">
                                <v-list-item-title> <!-- v-if="item.read_at == null" -->
                                    <h4>{{ item.data.subject }}</h4>
                                    <v-btn @click="readNotification(item.id)">Peminjaman ruangan {{ item.data.detailruangan.namaruangan }} untuk tanggal {{ new
                                        Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID', {
                                            year:
                                                'numeric', month:
                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                        }) }} - {{
                                            item.data.detailruangan.tanggalakhir }} <br> telah {{ item.data.namastatus }}
                                        oleh {{ item.data.accby }} pada {{ item.created_at }}. <br> Catatan : {{
                                            item.data.catatan }}
                                        </v-btn>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </template>
            </v-toolbar-items>
        </v-toolbar>

        <!-- <v-dialog style="justify-content:center;" v-model="isNotify" persistent max-width="500">
            <v-card
                style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 500px; height: 500px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="isNotify = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Pemberitahuan
                </v-card-title>
                <v-card-text style="text-align: center;">
                    Haloooowww
                </v-card-text>
            </v-card>
        </v-dialog> -->
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "headerUser",
    data() {
        return {
/*             isNotify: false,
 */            menusCenter: [
                { title: 'Ruangan', to: 'ruangan' },
                { title: 'Alat', to: 'alat' },
                { title: 'Beranda', to: 'berandaUser' },
                { title: 'Peminjaman Ruangan & Alat', to: 'peminjamanRuangan' },
                { title: 'Peminjaman Alat', to: 'peminjamanAlat' },
            ],
            menusLeft: [
                { title: 'Pemberitahuan', icon: 'mdi-bell' },
                { title: 'Pengaturan', icon: 'mdi-cog', submenus: [{ title: 'Profil', icon: 'mdi-account-circle', to: 'profil' }, { title: 'Logout', icon: 'mdi-logout' }] },
            ],
            UserID: localStorage.getItem("UserID"),
            allNotifications: []
        }
    },
    mounted() {
        // Check if user is logged in
        if (!localStorage.getItem("loggedIn")) {
            // Redirect to login page
            return this.$router.push({ name: 'loginPage' });
        };

        this.getNotification()
    },
    methods: {
        logout() {
            axios.get('http://localhost:8000/api/logout')
                .then(() => {
                    // Remove localStorage
                    localStorage.removeItem("loggedIn");
                    // Redirect
                    return this.$router.push({ name: 'loginPage' });
                })
                .catch(error => {
                    console.error('Logout failed:', error);
                });
        },
        getNotification() {
            axios.get(`http://127.0.0.1:8000/api/notifikasi/${this.UserID}`)
                .then((response) => {
                    this.allNotifications = response.data;
                    console.log(this.allNotifications);
                })
                .catch((error) => {
                    console.log(error)
                })
        },
        readNotification(id) {
            axios.get(`http://127.0.0.1:8000/api/notifikasiRead/${id}/${this.UserID}`)
                .then((response) => {
                    console.log(response.data);
                })
                .catch((error) => {
                    console.log(error)
                })
        }
    }
};
</script>

<style></style>