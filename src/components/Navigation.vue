<template>
  <div class="curPageContainer" v-if="(state.totalPages > 0 || state.totalUserPages > 0) && state.mode != 'token' || (state.search.string && state.totalPages>1)"
    :class="{'bumpLeft': !state.loggedin}">
    <button
      class="navButton"
      :class="{'disabled': curPage < 1}"
      :disabled="curPage < 1"
      @click="state.firstPage()"
    >
      &lt;&lt;
    </button>
    <button
      class="navButton"
      :disabled="curPage < 1"
      :class="{'disabled': curPage < 1}"
      @click="state.regressPage()"
    >
      &lt;
    </button>
    {{state.pagenumber()}}
    <button
      class="navButton"
      :class="{'disabled': totalPages == curPage+1}"
      :disabled="totalPages == curPage+1"
      @click="state.advancePage()"
    >
      &gt;
    </button>
    <button
      class="navButton"
      :class="{'disabled': totalPages == curPage+1}"
      :disabled="totalPages == curPage+1"
      @click="state.lastPage()"
    >
      &gt;&gt;
    </button>
  </div>
</template>

<script>

export default{
  name: 'Navigation',
  props: [ 'state'],
  components: {
    },
  data(){
    return{
    }
  },
  computed: {
    totalPages(){
      switch(this.state.mode){
        case 'user': return +this.state.totalUserPages; break
        case 'token': return +this.state.totalPages; break
        default: return +this.state.totalPages; break
      }
    },
    curPage(){
      switch(this.state.mode){
        case 'user': return +this.state.curUserPage; break
        case 'token': return +this.state.curPage; break
        default: return +this.state.curPage; break
      }
    }
  },
  methods:{
  },
  mounted(){
  }
}
</script>

<style>
.navContainer{
  margin-top: -50px;
  top:0;
  font-size: .7em;
  margin-left: auto;
  margin-right: auto;
  width: 600px!important;
	height: 100%;
  position: relative;
  z-index: 0;
  display: inline-block;
}
.curPageContainer{
  line-height: .8em;
  margin-top: 25px;
  min-height: 25px;
  margin-left: 0px;
  position: relative;
  vertical-align: top;
  padding-top: 0px;
  font-size: .8em;
  z-index: 50;
  display: inline-block;
}
.navButton{
  min-width:0;
  height: 25px;
  padding: 0;
  background: #0cc;
  margin: 0;
  margin-left: 2px;
  margin-right: 2px;
  width: 25px;
}
.disabled{
  background: #888!important;
  color: #000!important;
}
</style>
