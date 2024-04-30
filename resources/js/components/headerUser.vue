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
                    <v-btn v-else flat :key="menu1.title">
                        <v-icon left dark>{{ menu1.icon }}</v-icon>
                    </v-btn>
                </template>
            </v-toolbar-items>
        </v-toolbar>
    </div>
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
</template>

<script>
import axios from 'axios';

export default {
    name: "headerUser",
    data() {
        return {
            menusCenter: [
                { title: 'Beranda', to: 'berandaUser' },
                { title: 'Peminjaman Ruangan', to: 'peminjamanRuangan' },
                { title: 'Peminjaman Alat', to: 'peminjamanAlat' },
                { title: 'Ruangan', to: 'ruangan'},
            ],

            menusLeft: [
                { title: 'Pemberitahuan', icon: 'mdi-bell' },
                { title: 'Pengaturan', icon: 'mdi-cog', submenus: [{ title: 'Profil', icon: 'mdi-account-circle', to: 'profil' }, { title: 'Logout', icon: 'mdi-logout' }] },
            ]
        }
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
        }
    }
};
</script>

<style></style>