<template>
  <div class="siteWalletModalContainer">
    <div class="mainContainer" :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      <span style="color: #08f">Do you have a plorg.dweet.net wallet?</span>
      <br>
      <div class="spacerDiv" style="margin-top: 20px;"></div>
      <br>
      LOGIN<br>
      <input
        style="position: absolute; z-index:-1; opacity: 0;z-index: -1"
        ref="loginTabAnchor"
        type="text"
        v-on:keydown.enter="submit()"
        v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
      >
      <input
        type="text"
        ref="userName"
        v-model="userName"
        spellcheck="false"
        class="input"
        v-on:keydown.enter="login()"
        @click="$event.target.select()"
        v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
        v-on:keydown.esc="state.closePrompts()"
        placeholder="user name / tezos address / email - any of these"
      ><br>
      <input
        type="password"
        ref="password"
        v-model="password"
        spellcheck="false"
        v-on:keydown.enter="login()"
        class="input"
        @click="$event.target.select()"
        v-on:keydown.esc="state.closePrompts()"
        placeholder="password"
      ><br>
      <button class="loginButton"
        :disabled="!password || !userName"
        v-on:keydown.esc="state.closePrompts()"
        :class="{'disabledButton': !password || !userName}"
        @click="login()"
      >
        login        
      </button>
      <div v-if="state.invalidLoginAttempt" class="invalidLogin" style="position: absolute;left: 50%; transform: translate(-50%)">
        Invalid User Name or Password
      </div>
      <br>
      <div class="spacerDiv" style="margin-top: 20px;"></div>
      <br>
      <span style="color: #08f">OR</span><br><br>
      CREATE A WALLET<br>

      <div class="inputContainer">
        <div class="inputTitle">
          user name
          <div v-if="!userNameAvailable" style="display: inline-block; color: #f00;position: absolute;">
            &nbsp;&nbsp;&nbsp; <i>user name unavailable</i>
          </div>
          <div v-if="userNameAvailable && regusername" style="display: inline-block; color: #0f4;position: absolute;">
            &nbsp;&nbsp;&nbsp; <i>user name available</i>
          </div>
        </div>
        <input
          type="text"
          ref="regusername"
          spellcheck="false"
          v-model="regusername"
          class="input"
          maxlength="16"
          :class="{'userNameUnavailable': !userNameAvailable, 'userNameAvailable': userNameAvailable && regusername}"
          @input="checkUserNameAvailability()"
          v-on:keydown.esc="state.closePrompts()"
          v-on:keydown.enter="submit()"
          placeholder="user name"
        ><br>
      </div>
      <div class="inputContainer">
        <div class="inputTitle">
          recovery email (optional)
        </div>
        <input
          type="text"
          ref="regemail"
          spellcheck="false"
          v-model="regemail"
          class="input"
          maxlength="32"
          v-on:keydown.esc="state.closePrompts()"
          v-on:keydown.enter="submit()"
          placeholder="email address"
        ><br>
      </div>
      <div class="inputContainer">
        <div class="inputTitle">
          password
          <div v-if="!passwordsMatch && regpassword && confirmpassword" style="display: inline-block; color: #f00;position: absolute;">
            &nbsp;&nbsp;&nbsp; passwords do not match
          </div>
          <div v-if="passwordsMatch && regpassword && confirmpassword" style="display: inline-block; color: #0f4;position: absolute;">
            &nbsp;&nbsp;&nbsp; <i>passwords match!</i>
          </div>
        </div>
        <input type="password"
          ref="regpassword"
          v-model="regpassword"
          spellcheck="false"
          class="input"
          :class="{'passwordsDoNotMatch': !passwordsMatch && regpassword && confirmpassword, 'passwordsMatch': passwordsMatch && regpassword && confirmpassword}"
          v-on:keydown.esc="state.closePrompts()"
          v-on:keydown.enter="submit()"
          placeholder="password"
        ><br>
      </div>
      <div class="inputContainer">
        <div class="inputTitle">confirm password</div>
        <input
          type="password"
          ref="confirmpassword"
          v-model="confirmpassword"
          spellcheck="false"
          class="input"
          :class="{'passwordsDoNotMatch': !passwordsMatch && regpassword && confirmpassword, 'passwordsMatch': passwordsMatch && regpassword && confirmpassword}"
          v-on:keydown.enter="submit()"
          v-on:keydown.esc="state.closePrompts()"
          placeholder="confirm password"
        ><br>
      </div>
      <button
        @click="submit()"
        style="background-color:#4f8"
        :disabled="!validate"
        v-on:keydown.esc="state.closePrompts()"
        :class="{'disabledButton': !validate}"
      >submit</button>

      <button
        @click="state.closePrompts()"
        v-on:keydown.tab="$refs.loginTabAnchor.focus()"
        v-on:keydown.shift.tab="$refs.cancelButton.focus()"
        v-on:keydown.esc="state.closePrompts()"
        ref="cancelButton"
        class="cancelButton"
      >cancel</button>
      <div v-if="showInvalid" class="invalidLogin">
        Whoa! check your info...
      </div>
      <input
        style="position: absolute;; z-index:-1; opacity: 0;z-index: -1"
        ref="bottomTabAnchor"
        type="text"
      >
    </div>
  </div>
</template>

<script>

export default{
  name: 'siteWalletModal',
  props: [ 'state' ],
  data(){
    return{
      userName: '',
      password: '',
      regusername: '',
      regpassword: '',
      regemail: '',
      confirmpassword: '',
      userNameAvailable: true,
      showInvalid: false
    }
  },
  computed: {
    passwordsMatch(){
      return this.regpassword === this.confirmpassword
    },
    validate(){
      return this.regusername && this.regpassword && this.passwordsMatch && this.userNameAvailable
    }
  },
  methods:{
    login(){
      let userName = this.userName
      let password = this.password
      let sendData = {token: userName, password}
      fetch(this.state.baseURL + '/login.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          if(data[0]==2){
            this.state.renewPassToken = this.userName
            this.state.renewPassEmailRequired = !!(+data[9])
            this.state.renewPassHash = data[8]
            this.state.renewPassPkh = data[4]
            this.state.renewPassMode = +data[10]
            this.state.showRenewPasswordModal = true
            this.state.modalShowing = true
          } else {
            if(this.state.templeWalletConnected)this.state.templeSyncButtonClick()
            this.state.templeWalletConnected=false
            this.state.loggedinUserName = data[1]
            this.state.loggedinUserID = data[2]
            this.state.loggedinUserAvatar = data[3] ? data[3] : this.state.defaultAvatar
            this.state.loggedinUserPkh = data[4]
            this.state.loggedinUserHash = data[8]
            this.state.passhash = data[7]
            this.state.walletData = JSON.parse(JSON.stringify(this.state.defaultWalletData))
            this.state.walletData.address = data[4]
            this.state.isAdmin = (!!data[5])
            this.state.siteWalletConnected = true
            this.state.setCookie()
            this.state.checkSiteWallet()
            this.state.showSiteWalletModal = false
            this.state.getSiteWalletData()
            //setTimeout(()=>{this.state.loggedin = true},200)
            window.location.reload()
          }
        } else {
          this.state.invalidLoginAttempt = true
          setTimeout(()=>this.state.invalidLoginAttempt = 0, 2000)
        }
      })
    },
    submit(){
      if(this.validate){
        let sendData = {userName: this.regusername, password: this.regpassword, email: this.regemail}
        fetch(this.state.baseURL + '/submitReg.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(data[0]){
            if(this.state.templeWalletConnected)this.state.templeSyncButtonClick()
            this.state.templeWalletConnected=false
            this.state.loggedinUserName = data[1]
            this.state.loggedinUserID = data[2]
            this.state.loggedinUserAvatar = data[3] ? data[3] : this.state.defaultAvatar
            this.state.loggedinUserPkh = data[4]
            this.state.walletData = JSON.parse(JSON.stringify(this.state.defaultWalletData))
            this.state.walletData.address = data[4]
            this.state.passhash = data[7]
            this.state.loggedinUserHash = data[9]
            this.state.siteWalletConnected = true
            this.state.isAdmin = (!!data[5])
            this.state.checkSiteWallet()
            this.state.showSiteWalletModal = false
            this.state.getSiteWalletData()
            let newWallet = data[8]
            this.state.walletData.address = data[4]
            this.state.loggedinUserPkh = newWallet[1]
            this.state.seedPhrase = newWallet[2]
            this.state.setCookie()
            setTimeout(()=>{this.state.loggedin = true},200)
            this.state.showSeedPhraseModal = true
          } else {
            this.showInvalid = true
            setTimeout(()=>this.showInvalid = 0, 2000)
          }
        })
      } else {
        this.showInvalid = true
        setTimeout(()=>this.showInvalid = 0, 2000)
      }
    },
    checkUserNameAvailability(){
      this.userNameAvailable = true
      if(this.regusername){
        let sendData = {userName: this.regusername}
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
    this.$refs.userName.focus()
    document.getElementsByTagName('HTML')[0].style.overflowY = 'hidden'
  }
}
</script>

<style scoped>
.siteWalletModalIframe{
  width: calc(100%);
  height: 590px;
  max-width: 560px;
  max-height: 560px;
  overflow: hidden;
  border: none;
  border-top-left-radius: 50px;
  border-bottom-left-radius: 50px;
  border-radius: 50px;
  background: #000c;
  top: 20px;
  left: 50%;
  transform: scale(.8) translatex(-62%);
  margin-top: 20px;
  position: absolute;
  display: inline-block;
}
.siteWalletModalContainer{
  position: fixed;
  width: 100vw;
  height: 100vh;
  z-index: 100;
  top: 0;
  background: #000c;
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

