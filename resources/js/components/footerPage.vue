<template>
    <div class="flex" style="color:black; padding-top: 30px;">
        <div class="d-flex w-100 align-center px-4" style="background-color: #BBDEFB; padding-top: 20px; padding-bottom: 20px;">
            <div>
                <v-icon style="margin-top: 8px;">mdi-map-marker-outline</v-icon>
                <br>
                <br>
                <v-icon style="margin-bottom: 3px;">mdi-email-outline</v-icon>
                <v-spacer></v-spacer>
                <v-icon style="margin-bottom: 5px;">mdi-email-outline</v-icon>
            </div>
            <div>
                <p style="font-family: 'Lexend-Regular'; padding-left: 8px;">Jl. Dr. Wahidin Sudirohusodo No.5-25,
                    Kotabaru, Kec. Gondokusuman, <br> Kota Yogyakarta, Daerah Istimewa Yogyakarta 55224 (Gedung
                    Agape)</p>
                <v-spacer></v-spacer>
                <p style="font-family: 'Lexend-Regular'; padding-left: 8px; padding-top: 3px;">lab.fti.04@gmail.com
                </p>
                <v-spacer></v-spacer>
                <p style="font-family: 'Lexend-Regular'; padding-left: 8px; padding-top: 5px;">labfti3@gmail.com</p>
            </div>
            <v-spacer></v-spacer>
            <div>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2201.330970899941!2d110.3768124282516!3d-7.786221836516484!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5828f6fb7ef7%3A0xb710cc588f40770e!2sUniversitas%20Kristen%20Duta%20Wacana!5e0!3m2!1sen!2sid!4v1711092107297!5m2!1sen!2sid"
                    width="400px" height="200px" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <v-spacer></v-spacer>
            <div>
                <v-btn v-bind:href="'https://www.instagram.com/ftiukdw'"
                    style="border-radius: 50%; box-shadow: none; height: 60px; width: 0px; margin-top: 120px;">
                    <v-icon style="color: #0D47A1; font-size: 40px;">mdi-instagram</v-icon>
                </v-btn>
            </div>
        </div>
        <div class="px-4 py-2 text-center w-100"
            style="background-color: #0D47A1; color: white; font-family: 'Lexend-Regular';">
            Copyright &copy; 2024
        </div>
    </div>
</template>

<script>

export default {
    name: "footerPage",
    components: {
    }
}
</script>

<style lang="scss" scoped></style>