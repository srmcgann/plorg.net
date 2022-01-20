<template>
  <div class="UserSettings">
    <div class="mainContainer" :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      <span style="font-size: 18px;line-height: 1em;font-weight: 900;" v-html="state.userNameString()"></span>
      <span style="font-size: 28px;line-height: 1em;font-weight: 900;">
        <br><br><span style="color: #aff;font-size: 36px;font-weight: 400">settings</span>
      </span><br>
      <div class="spacerDiv" style="margin-top: 20px;"></div>
      your user name<br>
      <span style="margin-top:5px;font-size:.6em;float:left;vertical-align: top;text-align: left;line-height: .95em;">type a new name<br>and hit enter</span>
      <div v-if="!userNameAvailable" style="display: inline-block; color: #f00;position: absolute;font-size:.7em;margin-top:-20px;margin-left: 160px;">
        &nbsp;&nbsp;&nbsp; <i>user name unavailable</i>
      </div>
      <div v-if="userNameAvailable && newUserName" style="display: inline-block; color: #0f4;position: absolute;font-size:.7em;margin-top:-20px;margin-left: 160px;">
        &nbsp;&nbsp;&nbsp; <i>user name available</i>
      </div>
      <input style="opacity: 0; position: absolute;z-index: -1;" ref="tabAnchor"
        v-on:keydown.shift.tab="$refs.cancelButton.focus()"
      >
      <input
        type="text"
        maxlength="64"
        ref="userNameInput"
        spellcheck="false"
        v-model="newUserName"
        class="input avatarInput"
        :class="{'userNameUnavailable': !userNameAvailable, 'userNameAvailable': userNameAvailable && newUserName}"
        @input="checkUserNameAvailability()"
        v-on:keydown.shift.tab="$refs.endTabAnchor.focus()"
        v-on:keydown.enter="updateUserName()"
        placeholder="enter a new user name"
      >
      <div v-if="userNameUpdateSuccessful==1" style="color: #4f8; font-size: 18px; background:#055; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; userName update successful
      </div>
      <div v-if="userNameUpdateSuccessful==-1" style="color: #f44; font-size: 18px; background:#500; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; userName update NOT successful
      </div>
      <div class="spacerDiv" style="margin-top: 20px"></div>
      your avatar<br>
      <div class="editAvatarImgContainer" v-if="typeof state.getAvatar != 'undefined'">
        <img :src="state.loggedinUserAvatar" class="editAvatarImg">
      </div>
      <input
        type="text"
        ref="avatarURL"
        spellcheck="false"
        v-model="state.loggedinUserAvatar"
        class="input avatarInput"
        @input="updateAvatar()"
        @click="$event.target.select()"
        placeholder="URL to an online image..."
      ><br>
      <div v-if="avatarUpdateSuccessful==1" style="color: #4f8; font-size: 18px; background:#055; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; avatar update successful
      </div>
      <div v-if="avatarUpdateSuccessful==-1" style="color: #f44; font-size: 18px; background:#500; padding: 4px;margin-top:10px;padding-bottom: 8px;">
        &nbsp;&nbsp;&nbsp; avatar update NOT successful
      </div>
      <br>
      <button @click="closePrompt()"
        v-on:keydown.tab="$refs.tabAnchor.focus()"
        v-on:keydown.shift.tab="$refs.cancelButton.focus()"
        v-on:keydown.esc="state.closePrompts()"
        ref="cancelButton"
        class="cancelButton"
      >close / cancel [ESC]</button>
      <input style="opacity: 0; position: absolute;z-index: -1" ref="endTabAnchor"
        v-on:keydown.shift.tab="$refs.confirmnewassword.focus()"
      >
    </div>
  </div>
</template>


<script>

export default{
  name: 'UserSettings',
  props: [ 'state' ],
  data(){
    return {
      newPassword: '',
      confirmNewPassword: '',
      currentPassword: '',
      savedPassword: false,
      userNameAvailable: true,
      showInvalid: false,
      newUserName: this.state.loggedinUserName,
      avatarUpdateSuccessful: 0,
      userNameUpdateSuccessful: 0
    }
  },
  computed: {
    passwordsMatch(){
      return this.newPassword === this.confirmNewPassword
    },
    validate(){
      return this.passwordsMatch && this.newPassword && this.confirmNewPassword && this.currentPassword
    },
  },
  methods:{
    savePassword(){
      if(this.validate){
        let sendData = {userName: this.state.loggedinUserName, currentPassword: this.currentPassword, newPassword: this.newPassword}
        fetch(this.state.baseURL + '/changePassword.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(data[0]){
            this.state.passhash = data[1]
            this.savedPassword = true
            this.currentPassword = this.newPassword = this.confirmNewPassword = ''
            this.state.setCookie()
          } else {
            this.showInvalid = true
          }
        })
      } else {
        this.savedPassword = false
        this.showInvalid = true
      }
    },
    closePrompt(){
      this.state.closePrompts()
    },
    updateAvatar(){
      this.state.loggedinUserAvatar = this.state.loggedinUserAvatar.replace('"','').replace("'",'')
      let temp
      let sendData = {userName: this.state.loggedinUserName, pkh: this.state.walletData.address, newAvatar: (temp = this.state.loggedinUserAvatar)}
      fetch(this.state.baseURL + '/updateAvatar.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          this.state.loggedinUserAvatar = temp
          this.avatarUpdateSuccessful = 1
          this.state.users[this.state.loggedinUserID].avatar = this.state.loggedinUserAvatar
          setTimeout(()=>this.avatarUpdateSuccessful = 0, 2000)
        } else {
          this.avatarUpdateSuccssful = -1
          setTimeout(()=>this.avatarUpdateSuccessful = 0, 2000)
        }
      })
    },
    updateUserName(){
      if(this.checkUserNameAvailability() || this.newUserName == this.state.loggedinUserName) return
      let temp
      this.newUserName = this.newUserName.trimStart().trimEnd().replace('  ', ' ')
      if(!this.newUserName) return
      let sendData = {userName: this.state.loggedinUserName, pkh: this.state.walletData.address, newUserName: (temp = this.newUserName)}
      fetch(this.state.baseURL + '/updateUserName.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          this.userNameUpdateSuccessful = 1
          this.state.loggedinUserName = temp
          this.state.users[this.state.loggedinUserID].name = this.state.loggedinUserName
          if(this.state.mode == 'user'){
            window.history.replaceState('', '', '/u/' + this.state.loggedinUserName)
          }
          setTimeout(()=>this.userNameUpdateSuccessful = 0, 2000)
        } else {
          this.userNameUpdateSuccssful = -1
          setTimeout(()=>this.userNameUpdateSuccessful = 0, 2000)
        }
      })
    },
    checkUserNameAvailability(){
      this.newUserName = this.newUserName.trimStart().trimEnd().replace('  ', ' ')
      this.userNameAvailable = true
      if(this.newUserName == this.state.loggedinUserName) return true
      if(this.newUserName){
        let sendData = {userName: this.newUserName}
        fetch(this.state.baseURL + '/checkUserNameAvailability.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          this.userNameAvailable = data
        })
      }
    }
  },
  mounted(){
    this.$refs.userNameInput.focus()
    this.$refs.userNameInput.select()
    document.getElementsByTagName('HTML')[0].style.overflowY = 'hidden'
  }
}
</script>

<style>
.UserSettings{
  position: fixed;
  width: 100vw;
  height: 100vh;
  z-index: 100;
  top: 0;
  background: #000c;
  text-align: center;
  overflow-y: auto;
  overflow-x: hidden;
}
.mainContainer{
  padding: 20px;
  margin-top:20px;
  line-height: .85em;
  border: 1px solid #4ff2;
  min-width: 500px;
  max-width: 500px;
  position: absolute;
  top: 45%;
  left: 50%;
  font-size: 24px;
  transform: translate(-50%, -50%) scale(1);
}
.normalMainContainer{
  background: linear-gradient(-45deg, #103a, #000c, #023a);
}
.monochromeMainContainer{
  background: linear-gradient(-45deg, #222a, #000c, #222a);
}
.cancelButton{
  background: #800;
  color: #faa;
}
.inputTitle{
  text-align: left;
  width: 300px;
  margin-left: auto;
  margin-right: auto;
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

.inputContainer{
  padding: 10px;
}
.title{
  font-size: 2em;
}
.input{
  text-align: center;
  background: #0004;
  border: none;
  border-bottom: 1px solid #2fa4;
  width: calc(100% - 5px);
  font-size: 16px;
  color: #8fa;
}
.input:focus{
  outline: none;
}
.toggleButton{
  position: absolute;
  transform: translate(-55%,-36px);
  background: #6df;
}
.disabledButton{
  color: #888;
  background: #333;
}
.editAvatarImgContainer{
  margin-top: 15px;
  width: 250px;
  height: 250px;
  margin-left: auto;
  margin-right: auto;
  background: linear-gradient(135deg, #333, #000)
}
.editAvatarImg{
  margin-top: 50%;
  transform: translateY(-50%);
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
.passwordFields{
  text-align: center;
  background: #0004;
  border: none;
  border-bottom: 2px solid #2fa;
  width: calc(100% - 5px);
  color: #ffa;
  font-size: 22px;
  max-width: 320px;
}
.passwordsDoNotMatch{
  background: #b446!important;
}
.passwordsMatch{
  background: #4b46!important;
}
.avatarInput{
  font-size: 16px!important;
}
</style>

