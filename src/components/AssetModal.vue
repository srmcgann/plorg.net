<template>
  <div class="assetModal">
    <div class="mainContainer" :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      <div class="assetMain">
        <div class="itemHeaderTitle" style="padding-bottom:10px;">
          <div>
            {{item.title}}
          </div>
          <div class="itemHeaderDescription">
            {{item.description}}
          </div>
        </div>
        <div class="assetContainer" :class="{'initialHeight': item.type.indexOf('audio/')!==-1, 'normalAssetContainer':!state.monochrome,'monochromeAssetContainer':state.monochrome}">
          <button
            class="fullscreenButton"
            @click="toggleFullScreen()"
          >
          </button>
          <iframe v-if="item.type=='application/x-zip-compressed' || item.type=='application/zip'" class="assetFrame" :src="state.baseIPFSURL + '/' + item.ipfs" id="asset"></iframe>
          <Track
            v-else-if="typeof track.audioFile !== 'undefined' && item.type.indexOf('audio/')!==-1"
            :state="state" :track="track"
            id="asset"
           />
          <div
            v-else class="assetInner"
            id="asset"
            :class="{'portrait': assetLoaded && !(asset.width<asset.height), 'landscape': assetLoaded && (asset.width<asset.height)}"
            :style="'background-image:url('+state.baseIPFSURL + '/' + item.ipfs+')'"
          ></div>
        </div>
        <br>
      </div>
      <div v-if="state.loggedin" style="position: absolute;margin-top:-35px;font-size: .8em;color: #efeb;margin-left: 15px"> wallet {{this.state.walletData.balance}}ꜩ</div>
      <MintButton :state="state" :item="item"/>
      <button
        class="cancelButton"
        style="position: absolute;bottom: 0;right: 0;"
        @click="state.closePrompts()"
      >close [ESC]</button>
    </div>
  </div>
</template>

<script>
import MintButton from './MintButton'
import Track from './Track'

export default{
  name: 'assetModal',
  props: [ 'state', 'item' ],
  components: {
    MintButton,
    Track
  },
  data(){
    return{
      track: {},
      asset: null,
      assetLoaded: false,
    }
  },
  computed: {
  },
  methods:{
    toggleFullScreen(){
      let el = document.querySelector('#asset')
      this.state.toggleFullScreen(el)
    }
  },
  mounted(){
    if(this.item.type.indexOf('audio/')!==-1){
      this.track={
        id: null,
        userID: +this.item.creatorID,
        author: this.state.users[this.item.creatorHash].name,
        avatar: this.state.users[this.item.creatorHash].avatar,
        trackName: this.item.title,
        playlist:'',
        suffix: this.item.suffix,
        date: this.item.date,
        comments: [],
        private: 0,
        description: this.item.description,
        audioFile: this.state.baseIPFSURL + '/' + this.item.ipfs,
        plays: this.item.views,
        allowDownload: !!(+this.item.allowDownload)
      }
    } else if(this.item.type=='image/png' || this.item.type=='image/jpg' || this.item.type == 'image/jpeg' || this.item.type == 'image/gif'){
      this.asset = new Image()
      this.asset.onload=()=>{
        this.assetLoaded = true
      }
      this.asset.src = this.state.baseIPFSURL + '/' + this.item.ipfs
    }
    document.getElementsByTagName('HTML')[0].style.overflowY = 'hidden'
  }
}
</script>

<style scoped>
.assetModal{
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: #000c;
  position: fixed;
  top:0;
  left:0;
  z-index:500;
}
.itemHeaderTitle, .itemHeaderDescription{
  text-shadow: 2p 2px 2px #000;
  color: #fff;
}
.assetFrame{
  width: 100%;
  height: 100%;
  border: none;  
}
.mainContainer{
  border: 1px solid #4ff1;
  min-width: 625px;
  max-width: 625px;
  position: absolute;
  top: 45%;
  left: 50%;
  transform: translate(-50%, -50%) scale(.75);
}
.assetInner{
  width: 100%;
  height: 100%;
  background-position: center center;
  background-repeat: no-repeat;
}
.assetContainer{
  width: 625px;
  height:625px;
  margin-left: auto;
  margin-right: auto;
}
.assetMain{
  width: 100%;
  margin-bottom: 25px;
}
.cancelButton{
  background: #800;
  color: #faa;
  margin-right: 20px;
}
.initialHeight{
  height: initial;
}
</style>
