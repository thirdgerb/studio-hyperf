import Vue from 'vue'
import Vuetify, {
    VApp,
    VOverlay,
    VSubheader,
    VSystemBar,
    VImg,
    VDivider,
    VList,
    VListItem,
    VListItemTitle,
    VListItemIcon,
    VListItemAvatar,
    VListItemSubtitle,
    VListItemContent,
    VIcon,
    VAppBar,
    VAppBarNavIcon,
    VTabs,
    VTab,
    VTabsSlider,
    VTabsItems,
    VTabItem,
    VContent,
    VContainer,
    VCard,
    VCardActions,
    VCardText,
    VCardTitle,
    VNavigationDrawer,
    VToolbarTitle,
    VCol,
    VRow,
    VTooltip,
    VFooter,
    VBtn,
    VSpacer,
    VChip,
} from 'vuetify/lib'
import '@mdi/font/css/materialdesignicons.css'
import { Ripple } from 'vuetify/lib/directives'


Vue.use(Vuetify, {
    components : {
        VApp,
        VOverlay,
        VSubheader,
        VSystemBar,
        VImg,
        VDivider,
        VList,
        VListItem,
        VListItemTitle,
        VListItemIcon,
        VListItemAvatar,
        VListItemSubtitle,
        VListItemContent,
        VTabs,
        VTab,
        VTabsSlider,
        VTabsItems,
        VTabItem,
        VIcon,
        VAppBar,
        VAppBarNavIcon,
        VNavigationDrawer,
        VToolbarTitle,
        VContent,
        VContainer,
        VRow,
        VCol,
        VCard,
        VCardActions,
        VTooltip,
        VFooter,
        VBtn,
        VSpacer,
        VChip,
    },
    directives : {
        Ripple,
    },
    icons: {
        iconfont: 'mdi',
    },
})

const opts = {

}

export default new Vuetify(opts)
