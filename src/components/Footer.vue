<template>
  <div class="footer" v-html="state.footerMarkup" :class="{'normalFooter':!state.monochrome,'monochromeFooter':state.monochrome}">
  </div>
</template>

<script>
export default {
  name: 'Footer',
  props: [ 'state' ],
  data(){
    return {
    }
  },
  methods: {
  },
  computed: {
  },
  mounted(){
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .footer{
    position: fixed;
    bottom: 0;
    width: 100%;
    color: #fff;
    text-shadow: 1px 1px 2px #000;
    height: 25px;
    text-align: center;
  }
  .normalFooter{
    background: linear-gradient(90deg, #0438, #000, #2058);
  }
  .monochromeFooter{
    background: linear-gradient(90deg, #3338, #000, #3338);
  }
</style>
