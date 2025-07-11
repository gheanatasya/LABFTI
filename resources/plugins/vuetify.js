import "vuetify/styles";
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { VCalendar } from 'vuetify/labs/VCalendar'
import { VNumberInput } from 'vuetify/labs/VNumberInput'

const customeTheme = {
    dark: false,
    colors: {
        primary: "rgb(2,39, 10, 0.9)",
        secondary: "#424242",
        accent: "#82B1FF",
        error: "rgb(206, 0, 0, 0.91)",
        info: "#2196F3",
        success: "#4CAF50",
        warning: "#FFC107",
        lightblue: "#14c6FF",
        yellow: "#FFCF00",
        pink: "#FF1976",
        orange: "#FF8657",
        magenta: "#C33AFC",
        darkblue: "#1E2D56",
        gray: "#909090",
        neutralgray: "#9BA6C1",
        green: "#2ED47A",
        red: "#FF5c4E",
        darkblueshade: "#308DC2",
        lightgray: "#BDBDBD",
        lightpink: "#FFCFE3",
        white: "#FFFFFF",
        muted: "#6c757d",
    },
};

const vuetify = createVuetify({
    components,
    VCalendar,
    VNumberInput,
    directives,
    theme: {
        defaultTheme: "customeTheme",
        themes: {
            customeTheme,
            iconfont: 'mdi'
        },
    },
});

export default vuetify;

/* import Vue from 'vue'
import Vuetify from 'vuetify'

Vue.use(Vuetify)

const opts = {}

export default new Vuetify(opts)
 */