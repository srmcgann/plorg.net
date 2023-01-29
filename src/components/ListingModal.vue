<template>
  <div class="listingModal">
    <div class="mainContainer" :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      <div style="text-align: center;">list</div>
      <div class="spacerDiv" style="margin-bottom: 20px;"></div>
      <div v-if="stage==0">
        <div class="spaceHolder"></div>
        <div
          v-if="item.type.indexOf('image/') !== -1"
          :class="{'portrait': assetLoaded && (asset.width<asset.height), 'landscape': assetLoaded && !(asset.width<asset.height)}"
          class="imagePreview"
          :style="'background-image: url('+item.image+')'"
        >
        </div>
        <iframe
          v-else
          class="previewContainer"
          :src="state.baseIPFSURL + '/' + item.ipfs"
        ></iframe>
        <div v-if="state.uploadedContentURL" class="thumbnail"></div>
        <div class="spacerDiv" style="margin-bottom: 20px;"></div>
        <div style="line-height: 1.1em;font-size: 18px;">
          <span class="inputTitle">
            title
            <span class="subtitle">(max 128 chars)</span>
          </span>
          <input
            style="position: absolute; z-index:-1; opacity: 0;"
            ref="tabAnchor"
            type="text"
          >
          <input
            class="listInput"
            ref="title"
            v-model="item.title"
            maxlength="128"
            type="text"
            style="color: #aaa;"
            disabled="true"
            placeholder="title (max 128 chars)"
            v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
            v-on:keydown.enter="next()"
            @click="$event.target.select()"
          >
          <span class="inputTitle">
            description
            <span class="subtitle">(optional - max 512 chars)</span>
          </span>
          <input
            class="listInput"
            ref="description"
            v-model="item.description"
            style="color: #aaa;"
            type="text"
            disabled="true"
            maxlength="512"
            v-on:keydown.enter="next()"
            @click="$event.target.select()"
            placeholder="description (optional - max 512 chars)"
          >
          <span class="inputTitle">
            price
            <span class="subtitle">(Tezos)</span>
          </span>
          <input
            class="listInput"
            ref="price"
            v-model="item.price"
            type="text"
            :class="{'bad': invalidPrice}"
            @input="validatePrice()"
            v-on:keydown.enter="next()"
            @click="$event.target.select()"
            v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
            placeholder="price (Tezos)"
          >
          <div v-if="0">
            <span class="inputTitle">
              royalties
              <span class="subtitle">(whole number from 0% to 25%)</span>
            </span>
            <input
              class="listInput"
              ref="royalties"
              v-model="item.royalties"
              type="text"
              v-on:keydown.enter="next()"
              @input="validateRoyalties()"
              :class="{'bad': invalidRoyalties}"
              @click="$event.target.select()"
              placeholder="royalties (whole number from 0% to 25%)"
            >
          </div>
          <span class="inputTitle">
            editions (must be > current mints of this token)
            <span class="subtitle">({{(+item.mints)+1}} to 10,000)</span>
          </span>
          <input
            class="listInput"
            ref="editions"
            v-model="item.editions"
            type="text"
            @input="validateEditions()"
            :class="{'bad': invalidEditions}"
            v-on:keydown.enter="next()"
            @click="$event.target.select()"
            :placeholder="'editions ('+((+item.mints)+1)+' to 10,000)'"
          >
        </div>
        <button
          class="nextButton"
          ref="nextButton"
          @click="next()"
          v-on:keydown.enter="next()"
          :class="{'disabledButton': !validate}"
        >next</button>
        <button
          class="cancelButton"
          @click="state.closePrompts()"
          ref="cancelButton"
          v-on:keydown.tab="$refs.tabAnchor.focus()"
          v-on:keydown.shift.tab="$refs.cancelButton.focus()"
        >cancel [ESC]</button>
        <input
          style="position: absolute;; z-index:-1; opacity: 0;"
          ref="bottomTabAnchor"
          type="text"
        >
      </div>
      <div v-else-if="stage==1" style="margin-top:-15px;">
        <div style="display: inline-block;width: 100%;font-size: .7em;color:#ccc;text-align: center;margin-bottom: 20px;">thumbnail</div><br>
        <div class="spaceHolder"></div>
        <div v-if="state.uploadedFileType == 'application/x-zip-compressed' || state.uploadedFileType=='application/zip'">
          <div
            class="imagePreview"
            :class="{'portrait': assetLoaded && !(thumbnail.width<thumbnail.height), 'landscape': assetLoaded && (thumbnail.width<thumbnail.height),'showSpinner':shotInProgress}"
            :style="'background-image: url('+state.uploadedFileThumbnail+')'"
          >
            <div v-if="shotInProgress" style="margin-top:50%;transform: translatey(-50%);text-align: center;color: #fff;line-height:1.2em;font-size:1.9em;width:100%;text-shadow:2px 2px 2px #000;">
              getting<br>snapshot
            </div>
          </div>
          <button
            class="cancelButton"
            @click="genThumbnail()"
            ref="shootButton"
            style="background: #289;left: 0;color: #fff;text-shadow: 2px 2px 2px #000;padding-left: 20px;padding-right: 20px;position: relative; left: 50%;font-size: 24px;transform: translate(-50%);border-radius: 30px;margin-bottom: 5px;"
          >re-shoot</button><br>
          <span class="inputTitle">
            delay
            <span class="subtitle">(in ms,  1000 ms = 1 second)</span>
          </span>
          <input
            class="listInput"
            ref="thumbDelay"
            v-model="thumbDelay"
            maxlength="6"
            type="text"
            placeholder="screenshot delay (in ms,  1000 ms = 1 second)"
            v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
            v-on:keydown.enter="genThumbnail()"
            @click="$event.target.select()"
          >
          <span class="inputTitle">
            width
            <span class="subtitle">(default 800, max 1920)</span>
          </span>
          <input
            class="listInput"
            ref="thumbWidth"
            v-model="thumbWidth"
            maxlength="6"
            type="text"
            placeholder="screenshot width"
            v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
            v-on:keydown.enter="genThumbnail()"
            @click="$event.target.select()"
          >
          <span class="inputTitle">
            height
            <span class="subtitle">(default 800, max 1080)</span>
          </span>
          <input
            class="listInput"
            ref="thumbHeight"
            v-model="thumbHeight"
            maxlength="6"
            type="text"
            placeholder="screenshot height"
            v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
            v-on:keydown.enter="genThumbnail()"
            @click="$event.target.select()"
          >
          <div class="spacerDiv"></div>
          <button
            class="cancelButton"
            @click="next()"
            ref="shootButton"
            :class="{'disabledButton':shotInProgress}"
            :disabled="shotInProgress"
            style="background: #289;left: 0;color: #fff;text-shadow: 2px 2px 2px #000;padding-left: 20px;padding-right: 20px;position: absolute; left: 50%;font-size: 24px;transform: translate(-50%);border-radius: 30px;"
          >accept thumbnail</button>
          <br>
          <div class="spacerDiv"></div>
        </div>
        <div
          v-else-if="state.uploadedFileType.indexOf('image/') !== -1"
          class="imageThumb"
          :class="{'portrait': assetLoaded && !(thumbnail.width<asset.height), 'landscape': assetLoaded && (thumbnail.width<asset.height),'showSpinner':shotInProgress}"                    :style="'background-image: url('+state.uploadedFileThumbnail+')'"
          style="margin-bottom:20px;"
        ></div>
        <div
          v-else
          class="imageThumb"
          :class="{'portrait': assetLoaded && (asset.width<asset.height), 'landscape': assetLoaded && !(asset.width<asset.height),'showSpinner':shotInProgress}"                    :style="'background-image: url('+state.uploadedFileThumbnail+')'"
        ></div><br>
        <button
          class="cancelButton"
          @click="next()"
          ref="backButton"
          style="background: #289;left: 0;color: #fff;text-shadow: 2px 2px 2px #000;padding-left: 20px;padding-right: 20px;position: absolute; left: 50%;font-size: 24px;transform: translate(-50%);border-radius: 30px;"
        >accept thumbnail</button>
        <button
          class="cancelButton"
          @click="stage--;$nextTick(()=>{$refs.title.focus()})"
          ref="backButton"
          style="background: #486;left: 0;color: #fff;width: 140px;"
        >back</button>
      </div>
      <div v-else-if="stage==2" style="margin-top:-10px;">
        <div class="spaceHolder"></div>
        <div
          class="imagePreview"
          :class="{'portrait': assetLoaded && !(asset.width<asset.height), 'landscape': assetLoaded && (asset.width<asset.height),'showSpinner':shotInProgress}"          :style="'background-image: url('+item.image+')'"
        ></div>
        <div class="listInnerDiv">
          <span style="font-size: 1em;color:#ccc;">details</span><br>
          <div style="font-size: .6em;color: #aff;padding: 10px;">
            last chance to check everything before listing...
          </div>
          <div style="font-size: .6em;">
            <span class="itemTitle">title</span> {{item.title}}<br>
            <span class="itemTitle">description</span> {{item.description}}<br>
            <span class="itemTitle">price</span> {{item.price}}<br>
            <span v-if="0" class="itemTitle">royalties</span><span v-if="0">{{item.royalties}}%<br></span>
            <span class="itemTitle">editions</span> {{item.editions}}<br>
          </div>
        </div>
        <div class="spacerDiv"></div>
        <input
          style="position: absolute; z-index:-1; opacity: 0;"
          ref="tabAnchor"
          type="text"
        ><br>
        <button
          class="cancelButton"
          @click="list()"
          ref="backButton"
          style="background: #289;left: 0;color: #fff;text-shadow: 2px 2px 2px #000;position: absolute; left: 50%;font-size: 24px;transform: translate(-50%);border-radius: 30px;"
        >LIST!</button>
        <button
          class="cancelButton"
          @click="stage-=2;$nextTick(()=>{$refs.title.focus()})"
          ref="backButton"
          style="background: #486;left: 0;color: #fff;width:140px;"
        >back</button>
        <button
          class="cancelButton"
          @click="state.closePrompts()"
          ref="cancelButton"
          v-on:keydown.tab="$refs.bottomTabAnchor.focus()"
        >cancel [ESC]</button>
        <input
          style="position: absolute;; z-index:-1; opacity: 0;"
          ref="bottomTabAnchor"
          type="text"
        >
      </div>
    </div>
  </div>
</template>

<script>
import ListButton from './ListButton'

export default{
  name: 'listModal',
  props: [ 'state', 'item'],
  components: {
    ListButton
  },
  data(){
    return{
      title: '',
      thumbHeight: 800,
      thumbWidth: 800,
      shotInProgress: false,
      thumbDelay: 1000,
      description: '',
      metaData: '',
      price: '',
      editions: '',
      stage: 0,
      asset: {width: 800, height: 800},
      assetLoaded: false,
      royalties: '',
      thumbnail: {width: 800, height: 800},
      invalidPrice: false,
      invalidRoyalties: false,
      invalidEditions: false
    }
  },
  computed: {
    validate(){
      switch(this.stage){
        case 0:
        return this.item.title && this.validatePrice() && this.validateRoyalties() && this.validateEditions()
        break
        case 1:
        return this.item.title && this.validatePrice() && this.validateRoyalties() && this.validateEditions()
        break
        case 2:
        return this.item.title && this.validatePrice() && this.validateRoyalties() && this.validateEditions()
        break
        case 3:
        return this.item.title && this.validatePrice() && this.validateRoyalties() && this.validateEditions()
        break
      }
    }
  },
  methods:{
    getSnapshot(){
      let sendData = {
        pkh: this.state.walletData.address,
        url: this.state.uploadedContentURL,
        delay: this.thumbDelay,
        width: this.thumbWidth,
        height: this.thumbHeight
      }
      fetch(this.state.baseURL + '/screenshot.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res=>res.json()).then(data=>{
        this.shotInProgress = false
        if(data[0]){
          this.state.uploadedFileThumbnail = data[1]
          this.thumbnail = new Image()
          this.thumbnail.className='thumbnail_'
          this.thumbnail.onload=()=>{
            this.thumbWidth = this.thumbnail.width
            this.thumbHeight = this.thumbnail.height
          }
          this.thumbnail.src = this.state.uploadedFileThumbnail
          this.assetLoaded = true
        }
      })
    },
    genThumbnail(){
      this.shotInProgress = true
      if(this.state.uploadedFileType.indexOf('image/') !== -1){
        this.asset = new Image()
        this.asset.onload=()=>{
          let s = 1
          if(this.asset.width > this.asset.height){
            s=400/this.asset.width
          }else{
            s=400/this.asset.height
          }
          s=Math.min(1,s)
          this.thumbWidth = this.asset.width*s
          this.thumbHeight = this.asset.height*s
          this.getSnapshot()
        }
        this.asset.src = this.state.uploadedContentURL
      } else {
        this.getSnapshot()
      }
    },
    validatePrice(){
      let price = this.item.price
      if(isNaN(+price) || price<0 || price > this.state.maxPrice){
        this.invalidPrice = true
      } else {
        this.invalidPrice = false
      }
      return this.item.price && !this.invalidPrice
    },
    validateRoyalties(){
      let royalties = this.item.royalties
      if(isNaN(+royalties) || royalties<0 || royalties > 25){
        this.invalidRoyalties = true
      } else {
        this.invalidRoyalties = false
      }
      return this.item.royalties && !this.invalidRoyalties
    },
    validateEditions(){
      let editions = this.item.editions
      if(isNaN(+editions) || editions < (this.item.mints+1) || editions > this.state.maxEditions){
        this.invalidEditions = true
      } else {
        this.invalidEditions = false
      }
      return this.item.editions && !this.invalidEditions
    },
    next(){
      if(this.stage==2){
        this.list()
      }
      if(this.validate) this.stage+=2
    },
    list(){
      let l
      let sendData = {
        price: this.item.price,
        royalties: this.item.royalties,
        editions: this.item.editions,
        pkh: this.state.walletData.address,
        userHash: this.state.loggedinUserHash,
        userID: this.state.loggedinUserID,
        userName: this.state.loggedinUserName,
        itemID: this.item.id,
        itemHash: this.item.hash
      }
      fetch(this.state.baseURL + '/list.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json()).then(data => {
        if(data[0]){
          window.location.href = window.location.origin + '/t/' + this.state.decToAlpha(data[1])
        }
      })
    }
  },
  mounted(){
    if(this.item.type.indexOf('image/')!==-1){
      this.asset = new Image()
      this.asset.onload=()=>{
        this.assetLoaded = true
      }
      this.asset.src = this.item.image
    }
    this.$refs.price.focus()
    document.getElementsByTagName('HTML')[0].style.overflowY = 'hidden'
  }
}
</script>

<style scoped>
.listingModal{
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: #000d;
  position: fixed;
  top:0;
  left:0;
  font-size: 24px;
  z-index:150;
}
.bad{
  background: #6229;
  color: #faa;
}
.listInput{
  width: 100%;
  border-bottom: 1px solid #4fc4;
  font-size: 20px;
  margin-bottom: 20px;
  color: #af;
}
.inputTitle{
  color: #fff;
  font-size: 16px;
}
.itemHeaderTitle, .itemHeaderDescription{
  text-shadow: 2p 2px 2px #000;
  color: #fff;
}
.listInnerDiv{
  width:100%;
  padding: 10px;
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
  top: 47%;
  left: 50%;
  font-size: 32px;
  transform: translate(-50%, -50%) scale(.7);
}
.subtitle{
  color: #afc8;
}
.assetContainer{
  border: 1px solid #4ff2;
  width: 625px;
  height:625px;
  margin-left: auto;
  margin-right: auto;
  background: #000;
}
.assetMain{
  width: 100%;
}
.cancelButton, .nextButton{
  position: absolute;
  bottom: 0;
}
.cancelButton{
  margin-right: 20px;
  color: #faa;
  right: 0;
  background: #800;
}
.nextButton{
  margin-left: 20px;
  color: #afc;
  left: 0;
  background: #265;
}
.spaceHolder{
  width: 500px;
  height: 500px;
}
.thumbnail_{
 border: 1px solie #3ace4;
}
.previewContainer, .thumbnail{
  position: absolute;
  overflow: hidden!important;
  border: none;
}
.thumbnail{
  left: 75%;
}
.imagePreview, .imageThumb{
  left: 50%;
  transform: translate(-50%);
  margin-top:-510px;
  border: 1px solid #4fc2;
  background-position: center center;
  background-repeat: no-repeat;
  overflow: hidden;
  position: absolute;
}
.imagePreview{
  max-width: 500px;
  min-width: 500px;
  min-height: 500px;
  max-height: 500px;
}
.imageThumb{
  max-width: 500px;
  min-width: 500px;
  min-height: 500px;
  max-height: 500px;
}
.previewContainer{
  left: 50%;
  margin-top:-150px;
  min-width: calc(800px);
  min-height: 800px;
  height: 640px;
  max-height: 800px;
  border: 1px solid #4ff8;
  overflow: hidden;
  transform: translate(-50%,-505px) scale(.6);
}
</style>
<style>
.showSpinner{
  background-image: url(https://jsbot.whitehot.ninja/out_spedup.gif)!important;
  background-positon: center center!important;
  background-size: cover;
  border-radius: 50%;
  opacity: .8;
  border: none!important;
}
</style>
