<template>
  <div
    class="header"
    :class="{'shortHeader': minimized, 'normalHeader': !minimized, 'monochromeHeaderBackground': state.monochrome, 'normalHeaderBackground': !state.monochrome}"
    id="header"
  >
    <div
      :class="{'minimized': minimized, 'notMinimized': !minimized}"
      @click="toggleMinimized()"
    ></div>
      <div class="header-logo" :class="{'bigLogo':!minimized,'smallLogo':minimized,'smallNormalLogo':minimized && !state.monochrome,'smallMonochromeLogo': minimized && state.monochrome, 'bigNormalLogo': !minimized && !state.monochrome, 'bigMonochromeLogo': !minimized && state.monochrome}" @click="homepage()"></div>
    <div v-if="!minimized">
      <div style="width: 100%;min-width: 650px; position: relative;display: inline-block;height: 80px;max-height:80px;">
        <div style="position:relative;top:80px;left: 0;min-width: 580px;width: 100vw;text-align: center;border-top:1px solid #4ff2;">
          <div class="loginContainer" :class="{'bumpButtonDown': !state.loggedin, 'slimRightPadding': !!!state.loggedinUserName, 'normalRightPadding': !!state.loggedinUserName }">
            <div v-if="state.loggedin">
              <span class="loggedinusername"
                :class="{'highlightBackground':clicking}"
                @mousedown="doAddressClick()"
                @click="copyAddress()"
                v-html="state.userNameString()"
                title="click to copy tezos address"
              >
              </span>
            </div>
            <button v-if="state.siteWalletConnected" class="walletButton" @click="state.toggleShowTransactionModal()" title="open wallet"></button>
            <div v-if="state.templeWalletConnected" style="display: inline-block;">
              <button
                v-if="0"
                class="syncButton"
                :class="{'activeButton': state.templeWalletConnected && !state.monochrome, 'activeButtonMonochrome': state.templeWalletConnected && state.monochrome}"
                :title="state.templeWalletConnected?'unsync temple wallet':'sync temple wallet'"
                @click="state.templeSyncButtonClick()"
                v-html="templeButtonText()"
              >
              </button><br>
              <button
                class="syncButton"
                :class="{'activeButton': state.siteWalletConnected, 'normalSyncButton':!state.monochrome, 'monochromeSyncButton': state.monochrome}"
                :title="state.siteWalletConnected?'unsync plorg.dweet.net wallet':'sync plorg.dweet.net wallet'"
                @click="state.siteWalletSyncButtonClick()"
                v-html="siteButtonText()"
              >
              </button>
            </div>
            <div v-else style="display: inline-block;">
              <button
                class="syncButton"
                :class="{'activeButton':!state.monochrome, 'activeButtonMonochrome': state.monochrome}"
                :title="state.siteWalletConnected?'unsync plorg.dweet.net wallet':'sync plorg.dweet.net wallet'"
                @click="state.siteWalletSyncButtonClick()"
                v-html="siteButtonText()"
              >
              </button><br>
              <button
                v-if="0"
                class="syncButton"
                :class="{'activeButton': state.siteWalletConnected, 'normalSyncButton':!state.monochrome, 'monochromeSyncButton': state.monochrome}"
                :title="state.templeWalletConnected?'unsync temple wallet':'sync temple wallet'"
                @click="state.templeSyncButtonClick()"
                v-html="templeButtonText()"
              >
              </button>
            </div>
          </div>
          <div class="settingsContainer">
            <div v-if="state.loggedin" title="open your personal collection" @click="state.loadUserPage()" class="avatar" :style="'background-color: #000;background-image:url('+state.loggedinUserAvatar+')'"></div>
          </div>
          <UploadButton v-if="state.loggedin" :state="state" class="smallify"/>
        </div>
      </div>
    </div>
      <div class="transitionNav" style="margin-left: 0px;min-height: 80px; max-height: 80px;position: absolute;margin-left: auto; margin-right: auto; text-align: center;width: calc(100% - 250px);right: 0;"
        :class="{'smallTop':minimized , 'normalTop': !minimized}"
      >
      <div style="min-width:500px;">
        <div style="position: fixed;top:5px;left: 300px;margin-top:-5px;transform: scale(.7);">
          <label :for="'monochromeCheckbox'" class="checkboxLabel" style="width: 165px;text-align: left;" title="toggle monochrome theme">
            <input type="checkbox" :id="'monochromeCheckbox'" v-model="state.monochrome">
            <span class="checkmark" :class="{'normalCheckmark':!state.monochrome,'monochromeCheckmark':state.monochrome}"></span>
            <span>monochrome theme</span>
          </label>
        </div>
        <Navigation :state="state"/>
        <div
          @click="state.toggleShowControls()"
          v-if="1 || state.mode !== 'token'"
          title="search & other stuff [ESC]"
          :class="{'showImg':!state.showControls, 'hideImg': state.showControls, 'normalShowImgBG':!state.showControls && !state.monochrome,'monochromeShowImgBG': !state.showControls && state.monochrome,'normalHideImgBG':state.showControls && !state.monochrome,'monochromeHideImgBG': state.showControls && state.monochrome,'bump': !state.loggedin}"
        ></div>
        <img
          v-if="state.loggedin"
          title="open user settings"
          @click="toggleUserSettings()"
          class="settingsIcon"
          src="https://jsbot.whitehot.ninja/uploads/1vfDF7.png"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { TempleWallet } from "@temple-wallet/dapp"
import UploadButton from "./UploadButton"
import Navigation from "./Navigation"

export default {
  name: 'Header',
  data(){
    return{
      addresscopied: false,
      clicking: false,
      siteLogo: 'https://jsbot.whitehot.ninja/uploads/VCNU1.png',
      templeLogo: 'https://jsbot.whitehot.ninja/uploads/CxUEV.png'
    }
  },
  components:{
    UploadButton,
    Navigation
  },
  props: [ 'state' ],
  methods:{
    toggleMinimized(){
      this.state.minimized = !this.state.minimized
    },
    toggleUserSettings(){
      this.state.userSettingsVisible=!this.state.userSettingsVisible
      if(this.state.userSettingsVisible) this.state.modalShowing=true
    },
    homepage(){
      window.location.href='/'
    },
    templeButtonText(){
      if(this.state.templeWalletConnected){
        return 'unsync &nbsp;<img src="' + this.templeLogo + '" style="width:13px;position: absolute;margin-left:-8px;margin-top:2px;"/> Temple <span class="bal">' + this.state.walletData.balance + 'ꜩ '+this.formatDollars(this.state.XTZ_to_USD*this.state.walletData.balance)+'</span>'
      } else {
        return 'sync &nbsp;<img src="' + this.templeLogo + '" style="width:13px;position: absolute;margin-left:-8px;margin-top:2px;"/> Temple wallet'
      }
    },
    siteButtonText(){
      if(this.state.siteWalletConnected){
        return 'unsync &nbsp;<img src="' + this.siteLogo + '" style="width:25px;position: absolute;margin-left:-14px;"/> plorg.dweet.net <span class="bal">' + this.state.walletData.balance + 'ꜩ '+this.formatDollars(this.state.XTZ_to_USD*this.state.walletData.balance)+'</span>'
      } else {
        return 'sync &nbsp;<img src="' + this.siteLogo + '" style="width:20px;position: absolute;margin-left:-14px;"/> plorg.dweet.net wallet'
      }
    },
    doAddressClick(){
      this.clicking=true
      setTimeout(()=>{this.clicking=false},200)
    },
    copyAddress(){
      if(window.navigator.userAgent.toUpperCase().indexOf('FIREFOX')!=-1){
        var text = this.state.walletData.address;
        navigator.clipboard.writeText(text).then(function() {
          console.log('Async: Copying to clipboard was successful!');
        }, function(err) {
          console.error('Async: Could not copy text: ', err);
        });
      } else{
        var range = document.createRange()
        let el = document.createElement('div')
        el.innerHTML = this.state.walletData.address
        el.style.position='absolute'
        el.style.opacity=0
        document.body.appendChild(el)
        range.selectNode(el)
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range)
        document.execCommand("copy")
        window.getSelection().removeAllRanges()
        this.addresscopied = true;
        document.body.removeChild(el)
        setTimeout(()=>{
          this.addresscopied = false
        }, 250)
      }
    },
    formatDollars(val){
      val=val?val:0
      return '<span style="color: #6aa;font-size:.9em;">($' + Math.round(val*100)/100 + ')</span>'
    }
  },
  computed:{
    minimized(){
      return this.state.minimized
    },
    origin(){
      return window.location.origin
    }
  },
  mounted(){
    window.onresize=()=>{
      this.headerHeight=document.querySelector('#header').clientHeight
    }
  }
}
</script>

<style scoped>
  .header{
    overflow: hidden;
    border-bottom: 1px solid #4ff2;
    z-index: 5;
    width: 100%;
    min-width: 650px;
    top: 0;
    left: 0;
    transition: .3s min-height, .3s max-height;
  }
  .normalHeaderBackground{
    background: linear-gradient(50deg,#000c,#0008,#0006,#3068,#407a,#215b,#236c,#033c,#022c,#000c);
  }
  .monochromeHeaderBackground{
    background: linear-gradient(50deg,#000c,#0008,#0006,#3338,#444a,#444b,#333c,#222c,#111c,#000c);
  }
  .loginContainer{
    z-index: 1000;
    top: 0;
    margin-top: 0;
    display: inline-block;
    text-align: right;
    margin-left:80px;
    margin-top: 2px;
    vertical-align: top;
  }
  .settingsContainer{
    display: inline-block;
    margin-top: 6px;
    margin-left: 5px;
  }
  .header-logo{
    display: inline-block;
    width: 270px;
    margin: 0;
    cursor: pointer;
    z-index:50;
    position: fixed;
    left: 0;
    margin-left:-10px;
    height: 80px;
    background-repeat: no-repeat;
    background-position: -0 center;
    background-size: 100% 100%;
  }
  .balanceInfo{
    color: #fff;
    font-family: courier;
  }
  .settingsIcon{
    width: 45px;
    cursor: pointer;
    right: 0px;
    z-index: 1000;
    display: inline-block;
    margin-top:15px;
    margin-left: 20px;
    vertical-align: top;
  }  
  .hideImg{
    background-repeat: no-repeat;
    margin-top: 25px;
    width: calc(50px * 1.5);
    height: calc(19px * 1.5);
    background-size: 100% 100%;
    background-position: center center;
    display: inline-block;
    position: relative;
    margin-left: 20px;
    cursor: pointer;
  }
  .showImg{
    background-repeat: no-repeat;
    margin-top: 25px;
    margin-left: 20px;
    position: relative;
    width: calc(50px * 1.5);
    height: calc(19px * 1.5);
    background-size: 100% 100%;
    background-position: center center;
    display: inline-block;
    cursor: pointer;
  }
  .normalShowImgBG{
    background-image: url(https://jsbot.whitehot.ninja/uploads/1tgOjR.png);
  }
  .monochromeShowImgBG{
    background-image: url(https://jsbot.whitehot.ninja/uploads/23YRGp.png);
  }
  .normalHideImgBG{
    background-image: url(https://jsbot.whitehot.ninja/uploads/v9UDT.png);
  }
  .monochromeHideImgBG{
    background-image: url(https://jsbot.whitehot.ninja/uploads/1qoP9P.png);
  }
  .shortControlsContainer{
    height: 0px!important;
  }
  .walletButton{
    border: 2px solid #555a!important;
    border-radius: 0!important;
    margin: 0;
    vertical-align: top;
    padding: 0;
    position: relative;
    display: inline-block;
    min-width: initial;
    width: 45px;
    height: 45px;
    margin-right: -4px;
    background-image: url(https://jsbot.whitehot.ninja/uploads/OvenY.png);
    background-size: 43px;
    background-position: calc(50% + 1px) calc(50% - 2px);
    background-repeat: no-repeat;
    border-radius: 50%!important;
    padding:3px;
    background-color: #111f;
  }
  .highlightBackground{
    background-color: #0f8!important;  }
  .loggedinusername{
    transition: background-color .35s;
    font-weight: 900;
    font-size: .8em;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    display: inline-block;
    margin-top:0px;
    padding: 0;
    background-color: #000;
    padding-left: 10px;
    padding-right: 10px;
    margin-right: 5px;
    margin-bottom: 5px;
  }
  .activeButton{
    background-color: #0a0!important;
  }
  .activeButtonMonochrome{
    background-color: #888!important;
  }
  .smallify{
    margin-left: -25px;
    margin-right: 0;
    left: 0;
    margin-top: -40px;
    top: 0!important;
    transform: scale(.35);
    position: absolute!important;
  }
  .syncButton{
    color: #fff;
    font-weight: 900!important;
    text-shadow: 2px 2px 3px #000;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 15px;
    padding-top: 0;
    padding-bottom: 3px;
    margin-top: 10px;
    margin-bottom: 3px;
    min-width: 380px;
  }
  .normalSyncButton{
    background-color: #246!important;
  }
  .monochromeSyncButton{
    background-color: #333!important;
  }
  .logoutButton{
    min-width: initial;
    max-width: 70px;
    width: 70px;
    padding: 2px;
    padding-bottom: 4px;
    margin-right: 20px;
    background: #266;
    color: #4ff;
    text-shadow: 2px 2px 2px #000;
    font-size: 16px;
    font-weight: 400;
    float: right;
  }
  .avatar{
    width: 70px;
    height: 70px;
    right:5px;
    top: 5px;
    display: inline-block;
    background-position: center;
    background-size: cover;
    border-radius: 50%;
    cursor: pointer;
  }
  .bump{
    margin-top:20px;
  }
  .disabled{
    background: #888;
    color: #000;
  }
</style>
<style>
  .bal{
    display: inline-block;    
    margin-top: 2px;
    padding: 0;
    padding-left: 5px;
    padding-right: 5px;
    background: #000;
  }
  .bumpButtonDown{
    margin-right: 0;
    right: 0;
    margin-top: 18px!important;
  }
  .normalRightPadding{
    padding-right: 0px;
  }
  .enforceMaxHeight{
    max-height: 80px;
  }
  .slimRightPadding{
    padding-right: 5;
    right: 0;
  }
  .minimized, .notMinimized{
    width: 30px;
    height: 30px;
    right: 0;
    position: absolute;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    opacity: .8;
    margin-right: 10px;
    cursor: pointer;
    z-index:100;
    transition: .3s top;
  }
  .smallTop{
    top: -12px!important;
  }
  .normalTop{
    top: 5px!important;
  }
  .minimized{
    top: 12px;
    background-image: url(https://jsbot.whitehot.ninja/uploads/fjmMQ.png);  
  }
  .notMinimized{
    top: 120px;
    background-image: url(https://jsbot.whitehot.ninja/uploads/gyZ8s.png);  
  }
  .normalHeader{
    min-height: 160px;
    max-height: 160px;
  }
  .shortHeader{
    min-height: 50px;
    max-height: 50px;
  }
  .bigLogo{
    width: 270px;
  }
  .bigNormalLogo{
    background-image: url(https://jsbot.whitehot.ninja/uploads/LzPUq.png);
  }
  .bigMonochromeLogo{
    background-image: url(https://jsbot.whitehot.ninja/uploads/Emhnn.png);
  }
  .smallLogo{
    width: 350px!important;
    margin-left:0!important;
    left: 0!important;    
    height: 50px!important;
  }
  .smallNormalLogo{
    background-image: url(https://jsbot.whitehot.ninja/uploads/F1sbi.png);
  }
  .smallMonochromeLogo{
    background-image: url(https://jsbot.whitehot.ninja/uploads/118m22.png);
  }
  .transitionNav{
    transition: .3s top;
  }
</style>
