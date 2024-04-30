<template>
    <v-toolbar style="background-color: rgb(2,39,10,0.9); font-family: 'Lexend-Regular'">
        <v-toolbar-title>
            <router-link to="/" style="cursor: pointer">
                <v-img :width="80" cover src="fti-ukdw.png"></v-img>
            </router-link>
        </v-toolbar-title>
        <v-toolbar-items class="flex-grow-1 justify-center" style="color: white">
            <v-btn flat v-for="menu in menusCenter" :key="menu.title">
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
                                <v-icon>{{item.icon}}</v-icon>
                                <v-btn flat>
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
</template>

<script>
export default {
    name: "headerUser",
    data() {
        return {
            menusCenter: [
                { title: 'Beranda' },
                { title: 'Peminjaman Ruangan' },
                { title: 'Peminjaman Alat' },
                { title: 'Ruangan' },
            ],

            menusLeft: [
                { title: 'Pemberitahuan', icon: 'mdi-bell' },
                { title: 'Pengaturan', icon: 'mdi-cog', submenus: [{ title: 'Profil', icon: 'mdi-account-circle'}, { title: 'Logout', icon: 'mdi-logout'}] },
            ]
        }
    },
};
</script>

<style></style>