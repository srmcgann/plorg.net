<template>
  <div class="controls" v-if="!state.showUploadModal" :class="{'shortControlsContainer':!state.showControls, 'bumpup': state.minimized, 'normalControlsBackground': !state.monochrome, 'monochromeControlsBackground': state.monochrome}"
  :style="'top:'+(state.headerHeight*2+1)+'px'"
  >
    <div class="controlsWorkingSpace">
      <transition name="fade">
        <div v-if="state.showControls">
          <div class="navContainer">
            <div v-if="1||state.mode !== 'token'" class="advancedControls">
              <div :class="{'normalBG1': !state.monochrome, 'monochromeBG1': state.monochrome}" style="position: absolute; z-index:-1!important;width: 550px;height: 70px;opacity: .5;margin-top: -10px;margin-left: 60px;border-radius: 5px;"></div>
              <input id="searchInput" type="text" spellcheck="false" ref="searchInput" v-model="state.search.string" @input="state.beginSearch(1)" placeholder="search" class="textInput searchInput" style="display: inline-block;float: left;margin-bottom: 25px;margin-left: 75px;margin-top: 15px;position: relative!important: z-index:10000!important;">
              <label for="allWords" class="checkboxLabel" style="float: left;margin-left: 0px;margin-bottom:0px;display: inline-block;margin-left: 20px;margin-top: -2px;max-width:140px;font-size:.8em;">
                <input type="checkbox" :disabled="state.search.exact" :checked="state.search.allWords || state.search.exact" id="allWords" v-model="state.search.allWords" @input="state.beginSearch(1)">require all words
                <span class="checkmark" :class="{'normalCheckmark':!state.monochrome,'monochromeCheckmark':state.monochrome}"></span>
              </label><br>
              <label for="exact" class="checkboxLabel" style="float:left;display: inline-block;margin: 0;margin-top: 8px;margin-left: 20px;">
                <input type="checkbox" id="exact" v-model="state.search.exact" @input="state.beginSearch(1)">exact phrase
                <span class="checkmark" :class="{'normalCheckmark':!state.monochrome,'monochromeCheckmark':state.monochrome}"></span>
              </label>
              <div style="clear: both;"></div>
              <div v-if="state.search.hits" style="position: absolute;left: 50%;margin-left: -100px;margin-top: -80px;">
              ({{state.search.hits}} hits)
              </div>
              <button v-if="0" class="navButton" style="margin-left: 10px;font-size: 12px;min-height: 20px; height: 20px;width: 80px;margin-top: 5px;vertical-align: top;" title="hotkeys" @click="showHotkeys()">hotkeys</button>
              <button v-if="1" class="navButton" style="margin-left: 10px;font-size: 12px;min-height: 20px; height: 20px;width: 80px;margin-top: 5px;vertical-align: top;background:#800;color: #f6a;" title="hotkeys" @click="state.closeControls()">close</button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Controls',
  props: [ 'state' ],
  data(){
    return {
    }
  },
  methods:{
    showHotkeys(){
      alert("hotkeys cheat-sheet...\n\n")
    },
    updateUserPrefs(pref){
      this.$nextTick(()=>{
        let newval
        switch(pref){
          case 'demoPostsPerPage': newval = this.state.maxResultsPerPage; break
        }
        let sendData = {
          userName: this.state.loggedinUserName,
          passhash: this.state.passhash,
          pref,
          newval
       }
       fetch(this.state.baseURL + '/updatePrefs.php',{
         method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(pref == 'demoPostsPerPage') window.location.reload()
        })
      })
    },
    doReg(){
      this.state.showRegister = true
      this.state.showLoginPrompt()
    }
  },
  watch:{
    'state.showControls'(val){
      if(val){
        this.$nextTick(()=>{
          this.$refs.searchInput.focus()
          this.$refs.searchInput.select()
        })
      }
    }
  },
  computed:{
    totalPages(){
      switch(this.state.mode){
        case 'user': return +this.state.totalUserPages; break
        case 'default': return +this.state.totalPages; break
      }
    },
    curPage(){
      switch(this.state.mode){
        case 'user': return +this.state.curUserPage; break
        case 'default': return +this.state.curPage; break
      }
    }
  }
}
</script>

<style scoped>
.controls{
  position: fixed;
  z-index: 100;
  width: 93%;
  height: 200px;
  max-width: 1240px;
  min-width: 675px;
  background-size: 100% 100%;
  background-position: center center;
  background-repeat: no-repeat;
  left: 50%;
  transform: translateX(-50%);
  font-size: 24px;
  text-align: center;
  transition: height .3s, top .3s;
}
.controlsWorkingSpace .controlsbutton{
  background: linear-gradient(90deg, #158d, #145d);
  color: #def;
  margin-top: -20px;
  margin-right:60px;
  margin-left:60px;
}
.controlsWorkingSpace button:focus{
  outline: none;
}
.controlsWorkingSpace{
  max-width: 1200px;
  width: calc(90% - 30px);
  position: relative;
  left: 50%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  transform: translate(-50%);
}
.appLogo{
  opacity: .6;
  width:130px;
  position: absolute;
  top:0;
  left: 50%;
  z-index: -1;
  margin-left: -60px;
  margin-top: 0px;
}
.hideImg{
  width: calc(50px * 1.5);
  height: calc(19px * 1.5);
  background: url(https://jsbot.whitehot.ninja/uploads/v9UDT.png) no-repeat;
  background-size: 100% 100%;
  background-position: center center;
  position: absolute;
  top: 10px;
  right: 0;
  cursor: pointer;
}
.shortControlsContainer{
  height: 0px!important;
}
.autoPlayLabel{
  position: absolute;
  left: calc(50% + 70px);
  margin-top: -45px;
  margin-left: 40px;
  font-size: .8em;
  line-height: .9em;
  text-align: left;
}
.autoPlayCheckbox{
  margin-left: -23px;
  position: absolute;
  transform: scale(2.0);
  margin-top: 11px;
}
.advancedControls{
  top: 0;
  display: inline-block;
  margin-left: auto;
  margin-right: auto;
  margin-top: 10px;
  margin-bottom: 10px;
  left: 50%;
  width: 100%;
}
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
}
.uploadButton{
  background: #0f4;
  width: 80px;
  display: inline-block;
  text-align: center;
  line-height: .8em;
  margin-top: 4px;
  margin-left: 20px;
  min-width: 0;
}
.navButton{
  min-width:0;
  height: 25px;
  padding: 0;
  background: #0f0;
  margin: 0;
  margin-left: 2px;
  margin-right: 2px;
  width: 25px;
}
.maxResultsLabel{
  margin-left: 5px;
}
.bumpLeft{
  margin-left: -100px;
}
.bumpDown{
  margin-top: 17px;
}
#searchInput{
  display: inline-block;
  margin-bottom: 10px;
  font-size: 24px;
  text-align: center;
}
.fade-enter-active, .fade-leave-active{
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to{
  opacity: 0;
}
.bumpup{
  top: 51px!important;
}
.bump{
  margin-top: -57px!important;
}
</style>
<style>
.normalControlsBackground{
  background-image: url(https://jsbot.whitehot.ninja/uploads/Bi6Kz.png);
}
.monochromeControlsBackground{
  background-image: url(https://jsbot.whitehot.ninja/uploads/1Mpufx.png);
}
.normalBG1{
  background: rgb(34, 68, 102);
}
.monochromeBG1{
  background: rgb(70, 70, 70);
}
</style>