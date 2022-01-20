<template>
  <div class="seedPhraseModalContainer">
    <div class="mainContainer" :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      <span style="color: #08f">WALLET CREATED!</span>
      <br><br>
      your new Tezos (XTZ) address is<br><br>
      <div v-if="pkhcopied" style="color:#0f8;position:absolute;left:50%;transform:translate(-50%);margin-top:-2px;">copied!</div>
      <span
        @click="copyPkhToClipboard()"
        v-else
        style="font-size: 16px;color: #aaa;position:absolute;left:50%;transform:translate(-50%);cursor:pointer;min-width: 400px;"
      >
        (click to copy tezos address)
      </span><br>
      <button
        @click="copyPkhToClipboard()"
        ref="newPkh"
        style="padding: 10px; background: #044;margin:20px;margin-top:0;font-size: 20px;color:#fff;border-radius: 10px;"
        v-html="this.state.walletData.address"
      >
      </button>
      
      <br><br>
      <div class="spacerDiv" style="margin-top: 20px"></div><br>

      your mneumonic (seed) phrase is<br><br>
      <div v-if="mnemoniccopied" style="color:#0f8;position:absolute;left:50%;transform:translate(-50%);margin-top:-5px;">copied!</div>
      <span
        @click="copyMnemonicToClipboard()"
        v-else
        style="font-size: 16px;color: #aaa;position:absolute;left:50%;transform:translate(-50%);cursor:pointer;"
      >
        (click to copy)
      </span><br>
      <button
        @click="copyMnemonicToClipboard()"
        ref="newMnemonic"
        style="padding: 10px; padding-top:5px; padding-bottom:5px;background: #044;margin:20px;margin-top:0px;font-size: 20px;color:#fff;border-radius: 10px;text-align: justify;line-height:1.5em;padding: 20px;"
        v-html="this.state.seedPhrase"
      >
      </button><br><br>
  
      <span style="color: #f00;font-weight: 900;">COPY & SAVE OR WRITE THIS DOWN!</span><br>
      it will be required to do<br>
      <b>ANYTHING</b> with your new wallet<br><br>

      <button
        @click="confirmSeed()"
        v-on:keydown.tab="$refs.loginTabAnchor.focus()"
        v-on:keydown.shift.tab="$refs.cancelButton.focus()"
        ref="cancelButton"
        class="cancelButton"
      >close</button>
    </div>
  </div>
</template>

<script>

export default{
  name: 'seedPhraseModal',
  props: [ 'state' ],
  data(){
    return{
      pkhcopied: false,
      mnemoniccopied: false
    }
  },
  computed: {
  },
  methods:{
    copyPkhToClipboard(){
      if(window.navigator.userAgent.toUpperCase().indexOf('FIREFOX')!=-1){
        var text = this.$refs.newPkh.innerHTML;
        navigator.clipboard.writeText(text).then(function() {
          console.log('Async: Copying to clipboard was successful!');
        }, function(err) {
          console.error('Async: Could not copy text: ', err);
        });
      } else{
        var range = document.createRange()
        range.selectNode(this.$refs.newPkh)
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range)
        document.execCommand("copy")
        window.getSelection().removeAllRanges()
        this.pkhcopied = true;
      }
      setTimeout(()=>{
        this.pkhcopied = false
      }, 250)
    },
    copyMnemonicToClipboard(){
      if(window.navigator.userAgent.toUpperCase().indexOf('FIREFOX')!=-1){
        var text = this.$refs.newMnemonic.innerHTML;
        navigator.clipboard.writeText(text).then(function() {
          console.log('Async: Copying to clipboard was successful!');
        }, function(err) {
          console.error('Async: Could not copy text: ', err);
        });
      } else{
        var range = document.createRange()
        range.selectNode(this.$refs.newMnemonic)
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range)
        document.execCommand("copy")
        window.getSelection().removeAllRanges()
        this.mnemoniccopied = true;
      }
      setTimeout(()=>{
        this.mnemoniccopied = false
      }, 250)
    },
    confirmSeed(){
      let l
      if(this.state.seedPhrase==(l=prompt('enter your seed phrase here to show you\'ve copied it'))){
        window.onbeforeunload=()=>null
        this.state.minimized = false
        this.state.setCookie()
        setTimeout(()=>{window.location.reload()},20)
      }else{
        if(l){
          alert('a wrong seed phrase was entered... try again')
          this.confirmSeed()
        }
      }
    }
  },
  mounted(){
    window.onbeforeunload=()=>{
      confirm("are you sure you want to leave this page?\n\nyou need to copy your mnemonic!")
    }
  }
}
</script>

<style scoped>
.seedPhraseModalContainer{
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
}
.inputContainer{
  padding: 10px;
  line-height: .8em;
}
.title{
  font-size: 2em;
}
.inputTitle{
  text-align: left;
  width: 300px;
  font-size:.8em;
  margin:0;
}
.toggleButton{
  position: absolute;
  transform: translate(-55%,-36px);
  background: #6df;
}
.userNameUnavailable{
  background: #b446!important;
}
.passwordsDoNotMatch{
  background: #b446;
}
.passwordsMatch, .userNameAvailable{
  background: #4b46!important;
}
.disabledButton{
  color: #888;
  cursor: default;
  background: #333!important;
}
.invalidLogin{
  color: #f43;
  background: #400;
  padding: 10px;
  width: 290px;
  margin-left: auto;
  margin-right: auto;
  font-size: 20px;
}
.mustRegister{
  font-size: 26px;
  color: #4f8;
  background: #258;
  padding: 10px;
}
</style>

