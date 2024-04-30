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
                                <v-btn :to="menu.route" flat>
                                    {{ item1.title }}
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <v-btn v-else :to="menu.route" flat :key="menu.title">
                    {{menu.title}}
                </v-btn>
            </template>
        </v-toolbar-items>
        <v-spacer></v-spacer>
        <v-toolbar-items class="hidden-xs-only" style="color: white">
            <template v-for="menu1 in menusLeft" :key="menu1.title">
                <v-menu v-if="menu1.title === 'Pengaturan'">
                    <template v-slot:activator="{ on, props }">
                        <v-btn :to="menu1.route" flat v-on="on" v-bind="props">
                            <v-icon left dark>{{ menu1.icon }}</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item v-for="(item, i) in menu1.submenus" :key="i">
                            <v-list-item-title>
                                <v-icon>{{item.icon}}</v-icon>
                                <v-btn :to="menu1.route" flat>
                                    {{ item.title }}
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <v-btn v-else :to="menu1.route" flat :key="menu1.title">
                    <v-icon left dark>{{ menu1.icon }}</v-icon>
                </v-btn>
            </template>
        </v-toolbar-items>       
    </v-toolbar>
</template>

<script>
import '@mdi/font/css/materialdesignicons.min.css';

export default {
    name: "landingPage",
    data() {
        return {
            menusCenter: [
                { title: 'Beranda', route: '/beranda' },
                { title: 'Peminjaman', submenus:[{title: 'Peminjaman Ruangan', route: '/peminjamanRuangan'}, {title: 'Peminjaman Alat', route: '/peminjamanAlat'}], isOpen: false, },
                { title: 'Daftar Alat', route: '/daftarAlat' },
                { title: 'Daftar Peminjaman', route: '/daftarPeminjaman' },
                { title: 'Ruangan', route: 'ruangan' },
            ],

            menusLeft: [
                { title: 'Pemberitahuan', icon: 'mdi-bell' },
                { title: 'Pengaturan', icon: 'mdi-cog', submenus: [{ title: 'Profil', icon: 'mdi-account-circle', route: '/profil'}, { title: 'Logout', icon: 'mdi-logout', route: '/landingPage'}] },
            ]
        }
    },
};
</script>

<style></style>