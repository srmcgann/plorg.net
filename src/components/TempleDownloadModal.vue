<template>
  <div class="TempleDownloadModalContainer">
    <div class="mainContainer"  :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      You must install the<br>Temple Wallet plugin first!
      <iframe class="TempleDownloadModalIframe" :src="state.baseURL + '/getTempleDownloadPage.php'" ref="templeIframe" id="templeIframe">
      </iframe><br>
      <button
        class="cancelButton"
        @click="state.closePrompts()"
        ref="cancelButton"
      >close</button>      
    </div>
  </div>
</template>


<script>

export default{
  name: 'TempleDownloadModal',
  props: [ 'state' ],
  methods:{
  }
}
</script>

<style scoped>
.TempleDownloadModalIframe{
  width: calc(100%);
  height: 590px;
  max-width: 560px;
  max-height: 560px;
  overflow: hidden;
  border: none;
  border-top-left-radius: 50px;
  border-bottom-left-radius: 50px;
  border-radius: 50px;
  background: #fff0;
  top: 20px;
  left: 50%;
  transform: scale(.8) translatex(-62%);
  margin-top: 20px;
  position: absolute;
  display: inline-block;
}
.TempleDownloadModalContainer{
  position: fixed;
  width: 100vw;
  height: 100vh;
  z-index: 100;
  top: 0;
  background: #012e;
  text-align: center;
}
.cancelButton{
  background: #800;
  color: #faa;
  position: absolute;
  bottom: 25px;
  transform: translateX(-50%);
}
</style>

