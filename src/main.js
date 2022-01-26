import { createApp } from 'vue'
import App from './App.vue'
import linkify from 'vue-linkify'

const app = createApp(App)
 
app.directive('linkified', linkify)
app.mount('#app')
