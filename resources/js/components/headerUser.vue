<template>
    <v-card>
        <div class="" style="position: fixed; top: 0; width: 100%;">
            <v-toolbar style="background-color: #0D47A1; font-family: 'Lexend-Regular'; ">
                <v-toolbar-title>
                    <router-link to="/" style="cursor: pointer">
                        <v-img :width="80" cover src="../picture/fti-ukdw.png"></v-img>
                    </router-link>
                </v-toolbar-title>
                <v-toolbar-items class="flex-grow-1 justify-center" style="color: white">
                    <v-btn flat v-for="menu in menusCenter" :key="menu.title" :to="menu.to"
                        style="text-transform: none;">
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
                                        <v-btn flat @click="logout" v-if="item.title === 'Logout'"
                                            style="text-transform: none;">
                                            {{ item.title }}
                                        </v-btn>
                                        <v-btn :to="item.to" v-else flat style="text-transform: none;">
                                            {{ item.title }}
                                        </v-btn>
                                    </v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <v-menu v-if="menu1.title === 'Pemberitahuan'">
                            <template v-slot:activator="{ on, props }">
                                <v-btn flat v-on="on" v-bind="props">
                                    <v-badge :content="this.unread">
                                        <v-icon left dark size="x-large">{{ menu1.icon }}</v-icon></v-badge>
                                </v-btn>
                            </template>

                            <v-list style="width: 600px; height: 500px;" v-if="this.getData === false && this.allNotifications.length > 0">
                                <v-list-item style="margin-bottom: -10px;">
                                    <v-btn @click="deleteAll(this.UserID)" style="color: #0D47A1; margin-left: 90px; background: none;
                                text-decoration: underline; box-shadow: none; text-transform: none; position: absolute; right: 0; margin-right: 5px; top: 0;
                                ">Hapus Semua Pemberitahuan</v-btn>
                                </v-list-item>

                                <v-list-item v-for="(item, i) in this.allNotifications" :key="i">
                                    <!-- <v-list-item-title v-if="this.allNotifications.length === 0">
                                    <div style="margin-right: 20px; text-align: justify;">
                                        <h4>{{ item.data.subject }}</h4>
                                        <p @click="readNotification(item.id)">Mohon maaf peminjaman ruangan {{
                                            item.data.Nama_ruangan }}
                                            yang kamu lakukan <br>untuk tanggal {{ new
                                                Date(item.data.tanggalAwal).toLocaleTimeString('id-ID',
                                                    {
                                                        year:
                                                            'numeric', month:
                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                    }) }} 
                                            pada LAB FTI <br>Universitas Kristen Duta Wacana telah dibatalkan. <br>Silahkan lakukan peminjaman
                                            ruangan yang lain. Terimakasih.
                                        </p>
                                    </div>
                                </v-list-item-title> -->

                                    <!-- status acc peminjaman -->
                                    <v-hover v-if="item.data.statusacc">
                                        <template v-slot:default="{ isHovering, props }" v-if="item.read_at === null">
                                            <v-list-item-title v-if="item.read_at === null" v-bind="props" :style="{
                                                backgroundColor: isHovering ? '#BBDEFB' : '#E3F2FD',
                                                cursor: 'pointer',
                                                paddingLeft: '10px',
                                                borderBottom: '1px solid rgb(0, 0, 0, 0.1)',
                                                paddingBottom: '15px',
                                                paddingRight: '10px',
                                                paddingTop: '10px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'space-between'
                                            }">
                                                <div style="margin-right: 20px; text-align: justify;">
                                                    <h4>{{ item.data.subject }}</h4>
                                                    <p v-if="item.data.catatan !== 'null'"
                                                        @click="readNotification(item.id)">Peminjaman ruangan {{
                                                            item.data.detailruangan.namaruangan }}
                                                        untuk tanggal <br>{{ new
                                                            Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }} - {{
                                                            new
                                                                Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                                    {
                                                                        year:
                                                                            'numeric', month:
                                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                    }) }} <br>telah {{
                                                            item.data.namastatus
                                                        }}
                                                        oleh {{ item.data.accby }} <br> pada {{ new
                                                            Date(item.created_at).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }}. <br>Catatan :
                                                        {{
                                                            item.data.catatan }}
                                                    </p>
                                                    <p v-else @click="readNotification(item.id)">Peminjaman ruangan {{
                                                        item.data.detailruangan.namaruangan }}
                                                        untuk tanggal <br>{{ new
                                                            Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }} - {{
                                                            new
                                                                Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                                    {
                                                                        year:
                                                                            'numeric', month:
                                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                    }) }} <br>telah {{
                                                            item.data.namastatus
                                                        }}
                                                        oleh {{ item.data.accby }} <br> pada {{ new
                                                            Date(item.created_at).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }}.
                                                    </p>
                                                </div>
                                                <v-icon size="small" style="color: #0D47A1;">mdi-circle</v-icon>
                                            </v-list-item-title>
                                        </template>
                                    </v-hover>

                                    <v-hover v-if="item.data.statusacc">
                                        <template v-slot:default="{ isHovering2, props2 }" v-if="item.read_at !== null">
                                            <v-list-item-title v-if="item.read_at !== null" v-bind="props2" :style="{
                                                backgroundColor: isHovering2 ? 'rgba(0, 0, 0, 0.1)' : '',
                                                cursor: 'pointer',
                                                paddingLeft: '10px',
                                                borderBottom: '1px solid rgb(0, 0, 0, 0.1)',
                                                paddingBottom: '15px',
                                                paddingRight: '10px',
                                                paddingTop: '10px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'space-between'
                                            }">
                                                <div style="margin-right: 20px; text-align: justify;">
                                                    <h4>{{ item.data.subject }}</h4>
                                                    <p v-if="item.data.catatan !== 'null'"
                                                        @click="readNotification(item.id)">Peminjaman ruangan {{
                                                            item.data.detailruangan.namaruangan }}
                                                        untuk tanggal <br>{{ new
                                                            Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }} - {{
                                                            new
                                                                Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                                    {
                                                                        year:
                                                                            'numeric', month:
                                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                    })
                                                        }} <br>telah {{
                                                            item.data.namastatus
                                                        }}
                                                        oleh {{ item.data.accby }} <br> pada {{ new
                                                            Date(item.created_at).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }}. <br>Catatan :
                                                        {{
                                                            item.data.catatan }}
                                                    </p>
                                                    <p v-else @click="readNotification(item.id)">Peminjaman ruangan {{
                                                        item.data.detailruangan.namaruangan }}
                                                        untuk tanggal <br>{{ new
                                                            Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }} - {{
                                                            new
                                                                Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                                    {
                                                                        year:
                                                                            'numeric', month:
                                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                    })
                                                        }} <br>telah {{
                                                            item.data.namastatus
                                                        }}
                                                        oleh {{ item.data.accby }} <br> pada {{ new
                                                            Date(item.created_at).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }}.
                                                    </p>
                                                </div>
                                            </v-list-item-title>
                                        </template>
                                    </v-hover>

                                    <!-- pembatalan -->
                                    <v-hover v-if="item.data.cancel">
                                        <template v-slot:default="{ isHovering3, props3 }" v-if="item.read_at === null">
                                            <v-list-item-title v-if="item.read_at === null" v-bind="props3" :style="{
                                                backgroundColor: isHovering3 ? '#BBDEFB' : '#E3F2FD',
                                                cursor: 'pointer',
                                                paddingLeft: '10px',
                                                borderBottom: '1px solid rgb(0, 0, 0, 0.1)',
                                                paddingBottom: '15px',
                                                paddingRight: '10px',
                                                paddingTop: '10px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'space-between'
                                            }">
                                                <div style="margin-right: 20px; text-align: justify;">
                                                    <h4>{{ item.data.subject }}</h4>
                                                    <p @click="readNotification(item.id)">Mohon maaf peminjaman ruangan
                                                        {{
                                                            item.data.Nama_ruangan }}
                                                        yang kamu lakukan <br>untuk tanggal {{ new
                                                            Date(item.data.tanggalAwal).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }}
                                                        pada LAB FTI <br>Universitas Kristen Duta Wacana telah
                                                        dibatalkan. <br>Silahkan lakukan peminjaman
                                                        ruangan yang lain. Terimakasih.
                                                    </p>
                                                </div>
                                                <v-icon size="small" style="color: #0D47A1;">mdi-circle</v-icon>
                                            </v-list-item-title>
                                        </template>
                                    </v-hover>

                                    <v-hover v-if="item.data.cancel">
                                        <template v-slot:default="{ isHovering4, props4 }" v-if="item.read_at !== null">
                                            <v-list-item-title v-if="item.read_at !== null" v-bind="props4" :style="{
                                                backgroundColor: isHovering4 ? 'rgba(0, 0, 0, 0.1)' : '',
                                                cursor: 'pointer',
                                                paddingLeft: '10px',
                                                borderBottom: '1px solid rgb(0, 0, 0, 0.1)',
                                                paddingBottom: '15px',
                                                paddingRight: '10px',
                                                paddingTop: '10px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'space-between'
                                            }">
                                                <div style="margin-right: 20px; text-align: justify;">
                                                    <h4>{{ item.data.subject }}</h4>
                                                    <p @click="readNotification(item.id)">Mohon maaf peminjaman ruangan
                                                        {{
                                                            item.data.Nama_ruangan }}
                                                        yang kamu lakukan <br>untuk tanggal {{ new
                                                            Date(item.data.tanggalAwal).toLocaleTimeString('id-ID',
                                                                {
                                                                    year:
                                                                        'numeric', month:
                                                                        'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                                }) }}
                                                        pada LAB FTI <br>Universitas Kristen Duta Wacana telah
                                                        dibatalkan. <br>Silahkan lakukan peminjaman
                                                        ruangan yang lain. Terimakasih.
                                                    </p>
                                                </div>
                                            </v-list-item-title>
                                        </template>
                                    </v-hover>
                                </v-list-item>
                            </v-list>

                            <v-list style="width: 600px; height: 500px;" v-if="this.allNotifications.length === 0 && this.getData === false">
                                <p
                                    style="color: #0D47A1; font-size: 25px; font-family: Lexend-Regular; margin-left: 20px; margin-top: 20px; margin-bottom: 10px;">
                                    Tidak ada pemberitahuan</p>
                            </v-list>
    
                            <v-list style="width: 600px; height: 500px;" v-if="this.getData === true">
                                <v-row align-content="center" class="fill-height" justify="center">
                                    <v-col class="text-subtitle-1 text-center" cols="12"
                                        style="font-family: Lexend-Regular;">
                                        Tunggu sebentar ya...
                                    </v-col>
                                    <v-col cols="1">
                                        <v-progress-circular color="#0D47A1" indeterminate></v-progress-circular> </v-col>
                                </v-row>
                            </v-list>
                        </v-menu>
                    </template>
                </v-toolbar-items>
            </v-toolbar>

            <!-- dialog logout -->
            <v-overlay v-model="overlay" style="background-color: white; z-index: 1">
                <v-container style="height: 660px; margin-left: 440px;">
                    <v-row align-content="center" class="fill-height" justify="center">
                        <v-col class="text-subtitle-1 text-center" cols="12" style="font-family: Lexend-Regular;">
                            Logout
                        </v-col>
                        <v-col cols="6">
                            <v-progress-linear color="#0D47A1" height="6" indeterminate rounded></v-progress-linear>
                        </v-col>
                    </v-row>
                </v-container>
            </v-overlay>
        </div>
    </v-card>
</template>

<script>
import axios from 'axios';

export default {
    name: "headerUser",
    data() {
        return {
            menusCenter: [
                { title: 'Ruangan', to: 'ruangan' },
                { title: 'Alat', to: 'alat' },
                { title: 'Beranda', to: 'berandaUser' },
                { title: 'Peminjaman Ruangan', to: 'peminjamanRuangan' },
                { title: 'Peminjaman Alat', to: 'peminjamanAlat' },
            ],
            menusLeft: [
                { title: 'Pemberitahuan', icon: 'mdi-bell' },
                { title: 'Pengaturan', icon: 'mdi-cog', submenus: [{ title: 'Profil', icon: 'mdi-account-circle', to: 'profil' }, { title: 'Logout', icon: 'mdi-logout' }] },
            ],
            UserID: localStorage.getItem("UserID"),
            allNotifications: [],
            unread: 0,
            overlay: false,
            getData: undefined,
        }
    },
    mounted() {
        // cek kalau user udah login
        if (!localStorage.getItem("loggedIn")) {
            return this.$router.push({ name: 'loginPage' });
        };

        this.getNotification()

        const loginTime = localStorage.getItem("loginTime");

        // kalau user login lebih dari sehari, otomatis logout
        if (loginTime) {
            const timeDiff = Date.now() - parseInt(loginTime);
            const oneDay = 24 * 60 * 60 * 1000;

            if (timeDiff >= oneDay) {
                localStorage.removeItem("loggedIn");
                localStorage.removeItem("token");
                localStorage.removeItem("UserID");
                localStorage.removeItem("User_role");
                localStorage.removeItem("Total_batal");
                localStorage.removeItem("loginTime");
                this.$router.push({ name: 'loginPage' });
            }
        }
    },
    methods: {
        logout() {
            this.overlay = true;
            axios.get('http://localhost:8000/api/logout')
                .then(() => {
                    localStorage.removeItem("loggedIn");
                    localStorage.removeItem("token");
                    localStorage.removeItem("UserID");
                    localStorage.removeItem("User_role");
                    localStorage.removeItem("Total_batal");
                    localStorage.removeItem("loginTime");
                    this.overlay = false;
                    return this.$router.push({ name: 'loginPage' });
                })
                .catch(error => {
                    console.error('Logout failed:', error);
                    this.overlay = false;
                });
        },
        getNotification() {
            this.getData = true;
            axios.get(`http://127.0.0.1:8000/api/notifikasi/${this.UserID}`)
                .then((response) => {
                    this.allNotifications = response.data.data;
                    this.unread = response.data.unread;
                    console.log(this.allNotifications);
                    this.getData = false;
                })
                .catch((error) => {
                    console.log(error)
                })
        },
        readNotification(id) {
            axios.get(`http://127.0.0.1:8000/api/notifikasiRead/${id}/${this.UserID}`)
                .then((response) => {
                    console.log(response.data);
                    this.getNotification();
                })
                .catch((error) => {
                    console.log(error)
                })
        },
        deleteAll(UserID) {
            axios.delete(`http://127.0.0.1:8000/api/deleteAll/${UserID}`)
                .then((response) => {
                    console.log(response.data);
                    this.getNotification();
                }).catch((error) => {
                    console.log(error)
                })
        }
    }
};
</script>

<style></style>