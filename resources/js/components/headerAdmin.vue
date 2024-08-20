<template>
    <v-toolbar style="background-color: rgb(2,39,10,0.9); font-family: 'Lexend-Regular'">
        <v-toolbar-title>
            <router-link to="/beranda" style="cursor: pointer">
                <v-img :width="80" cover src="fti-ukdw.png"></v-img>
            </router-link>
        </v-toolbar-title>
        <v-toolbar-items class="flex-grow-1 justify-center" style="color: white">
            <template v-for="menu in menusCenter" :key="menu.title">
                <v-menu v-if="menu.title === 'Peminjaman'">
                    <template v-slot:activator="{ on, props }">
                        <v-btn :to="menu.route" flat v-on="on" v-bind="props" @click="menu.isOpen = !menu.isOpen">
                            {{ menu.title }}
                            <v-icon>{{ menu.isOpen ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item v-for="(item1, j) in menu.submenus" :key="j">
                            <v-list-item-title>
                                <v-btn :to="item1.route" flat>
                                    {{ item1.title }}
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <v-btn v-else :to="menu.route" flat :key="menu.title">
                    {{ menu.title }}
                </v-btn>
            </template>
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
                                <v-btn :to="item.route" v-else flat>
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

                    <v-list style="width: 600px;">

                        <v-list-item v-for="(item, i) in this.allNotifications" :key="i">
                            <v-hover>
                                <template v-slot:default="{ isHovering, props }" v-if="item.read_at === null">
                                    <v-list-item-title v-if="item.read_at === null" v-bind="props" :style="{
                                        backgroundColor: isHovering ? 'rgba(3, 138, 33, 0.4)' : 'rgb(3, 138, 33, 0.3)',
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
                                        <div style="margin-right: 20px; text-align: justify;"
                                            v-if="item.data.subject === 'Status Peminjaman'">
                                            <h4>{{ item.data.subject }}</h4>
                                            <p @click="readNotification(item.id)">Peminjaman ruangan {{
                                                item.data.detailruangan.namaruangan }}
                                                untuk tanggal <br>{{ new
                                                    Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }} <br>- {{
                                                    new
                                                        Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                            {
                                                                year:
                                                                    'numeric', month:
                                                                    'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                            }) }} <br>telah {{
                                                    item.data.namastatus
                                                }}
                                                oleh {{ item.data.accby }} pada {{ new
                                                    Date(item.created_at).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}. <br>Catatan :
                                                {{
                                                    item.data.catatan }}
                                            </p>
                                        </div>

                                        <div style="margin-right: 20px; text-align: justify;"
                                            v-if="item.data.subject === 'Peminjaman Ruangan Baru'">
                                            <h4>{{ item.data.subject }}</h4>
                                            <p @click="readNotification(item.id)"> Peminjaman ruangan telah dilakukan!
                                                <br>
                                                Ruangan {{ item.data.detailruangan.namaruangan }} akan dipinjam pada
                                                tanggal
                                                {{ new
                                                    Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}<br> - {{
                                                    new
                                                        Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                            {
                                                                year:
                                                                    'numeric', month:
                                                                    'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                            }) }}. <br>
                                                Segera konfirmasi peminjaman!
                                            </p>
                                        </div>

                                        <div style="margin-right: 20px; text-align: justify;"
                                            v-if="item.data.subject === 'Peminjaman Alat Baru'">
                                            <h4>{{ item.data.subject }}</h4>
                                            <p @click="readNotification(item.id)"> Peminjaman alat telah dilakukan! <br>
                                            <ul>
                                                <li v-for="alat in item.data.detailalat" :key="alat">{{ alat.namaalat }}
                                                    : {{ alat.jumlahPinjam }}</li>
                                            </ul>
                                            Akan dipinjam pada tanggal
                                            {{ new
                                                Date(item.data.detailalat[0].tanggalawal).toLocaleTimeString('id-ID',
                                                    {
                                                        year:
                                                            'numeric', month:
                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                    }) }}<br> - {{
                                                new
                                                    Date(item.data.detailalat[0].tanggalakhir).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}. <br>
                                            Segera konfirmasi peminjaman!
                                            </p>
                                        </div>

                                        <v-icon size="small" style="color: rgb(2,39,10,0.9);">mdi-circle</v-icon>
                                    </v-list-item-title>
                                </template>
                            </v-hover>


                            <v-hover>
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
                                        <div style="margin-right: 20px; text-align: justify;" v-if="item.data.subject === 'Status Peminjaman'">
                                            <h4>{{ item.data.subject }}</h4>
                                            <p @click="readNotification(item.id)">Peminjaman ruangan {{
                                                item.data.detailruangan.namaruangan }}
                                                untuk tanggal <br>{{ new
                                                    Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}<br>- {{
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
                                                oleh {{ item.data.accby }} pada {{ new
                                                    Date(item.created_at).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}. <br>Catatan :
                                                {{
                                                    item.data.catatan }}
                                            </p>
                                        </div>

                                        <div style="margin-right: 20px; text-align: justify;"
                                            v-if="item.data.subject === 'Peminjaman Ruangan Baru'">
                                            <h4>{{ item.data.subject }}</h4>
                                            <p @click="readNotification(item.id)"> Peminjaman ruangan telah dilakukan!
                                                <br>
                                                Ruangan {{ item.data.detailruangan.namaruangan }} akan dipinjam pada
                                                tanggal
                                                {{ new
                                                    Date(item.data.detailruangan.tanggalawal).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}<br> - {{
                                                    new
                                                        Date(item.data.detailruangan.tanggalakhir).toLocaleTimeString('id-ID',
                                                            {
                                                                year:
                                                                    'numeric', month:
                                                                    'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                            }) }}. <br>
                                                Segera konfirmasi peminjaman!
                                            </p>
                                        </div>

                                        <div style="margin-right: 20px; text-align: justify;"
                                            v-if="item.data.subject === 'Peminjaman Alat Baru'">
                                            <h4>{{ item.data.subject }}</h4>
                                            <p @click="readNotification(item.id)"> Peminjaman alat telah dilakukan! <br>
                                            <ul>
                                                <li v-for="alat in item.data.detailalat" :key="alat">{{ alat.namaalat }}
                                                    : {{ alat.jumlahPinjam }}</li>
                                            </ul>
                                            Akan dipinjam pada tanggal
                                            {{ new
                                                Date(item.data.detailalat[0].tanggalawal).toLocaleTimeString('id-ID',
                                                    {
                                                        year:
                                                            'numeric', month:
                                                            'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                    }) }}<br> - {{
                                                new
                                                    Date(item.data.detailalat[0].tanggalakhir).toLocaleTimeString('id-ID',
                                                        {
                                                            year:
                                                                'numeric', month:
                                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                                        }) }}. <br>
                                            Segera konfirmasi peminjaman!
                                            </p>
                                        </div>
                                    </v-list-item-title>
                                </template>
                            </v-hover>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
        </v-toolbar-items>

        <!-- dialog logout -->
        <v-overlay v-model="overlay" style="background-color: white; z-index: 1">
            <v-container style="height: 660px; margin-left: 440px;">
                <v-row align-content="center" class="fill-height" justify="center">
                    <v-col class="text-subtitle-1 text-center" cols="12" style="font-family: Lexend-Regular;">
                        Logout
                    </v-col>
                    <v-col cols="6">
                        <v-progress-linear color="primary" height="6" indeterminate rounded></v-progress-linear>
                    </v-col>
                </v-row>
            </v-container>
        </v-overlay>
    </v-toolbar>
</template>

<script>
import '@mdi/font/css/materialdesignicons.min.css';

export default {
    name: "headerAdmin",
    data() {
        return {
            menusCenter: [
                { title: 'Ruangan', route: 'ruangan' },
                { title: 'Alat', route: 'alat' },
                { title: 'Beranda', route: 'berandaUser' },
                { title: 'Peminjaman', submenus: [{ title: 'Peminjaman Ruangan & Alat', route: 'peminjamanRuangan' }, { title: 'Peminjaman Alat', route: 'peminjamanAlat' }], isOpen: false, },
                { title: 'Daftar Alat', route: 'daftarAlat' },
                { title: 'Daftar Ruangan', route: 'daftarRuangan' },
                { title: 'Daftar Peminjaman', route: 'daftarPeminjaman' },
            ],

            menusLeft: [
                { title: 'Pemberitahuan', icon: 'mdi-bell' },
                { title: 'Pengaturan', icon: 'mdi-cog', submenus: [{ title: 'Profil', icon: 'mdi-account-circle', route: 'profil' }, { title: 'Logout', icon: 'mdi-logout', route: 'landingPage' }] },
            ],
            allNotifications: [],
            UserID: localStorage.getItem('UserID'),
            unread: 0,
            overlay: false,
        }
    },
    mounted() {
        // Check if user is logged in
        if (!localStorage.getItem("loggedIn")) {
            // Redirect to login page
            return this.$router.push({ name: 'loginPage' });
        };

        this.getNotification()

        const loginTime = localStorage.getItem("loginTime");

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
                    // Remove localStorage
                    localStorage.removeItem("loggedIn");
                    localStorage.removeItem("token");
                    localStorage.removeItem("UserID");
                    localStorage.removeItem("User_role");
                    localStorage.removeItem("Total_batal");  
                    localStorage.removeItem("loginTime");
                    this.overlay = false;
                    // Redirect
                    return this.$router.push({ name: 'loginPage' });
                })
                .catch(error => {
                    console.error('Logout failed:', error);
                    this.overlay = false;
                });
        },
        getNotification() {
            axios.get(`http://127.0.0.1:8000/api/notifikasi/${this.UserID}`)
                .then((response) => {
                    this.allNotifications = response.data.data;
                    this.unread = response.data.unread;
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