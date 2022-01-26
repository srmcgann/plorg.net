<template>
  <div class="main">
    <div class="mainContainer" :class="{'normalMainContainer': !state.monochrome,'monochromeMainContainer': state.monochrome}">
      <div style="text-align: center;">you must renew your password</div><br>
      <div class="spacerDiv" style="margin-bottom: 20px;"></div>
      <span class="disclaimerText">
        why am i seeing this?<br>
        this is probably a new node on the plorg network, and as we never store keys, mnemonics, passwords, nor password hashes on blockchain, you will need to re-enter a password for this server.
      </span>
      <br>
      <input
        style="position: absolute; z-index:-1; opacity: 0;z-index: -1"
        ref="tabAnchor"
        type="text"
      >
      <div v-if="state.renewPassEmailRequired" style="display: inline-block;">
        <span class="smallText"> enter the email you provided when you registered</span>
        <input
          type="text"
          v-model="email"
          ref="email"
          class="renewPasswordInput"
          placeholder="enter email"
          v-on:keydown.esc="state.closePrompts()"
          v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
          v-on:keydown.enter="submit()"
        >
        <br><br>
        <span class="smallText">enter your new password</span>
        <input
          type="password"
          v-model="newpass"
          class="renewPasswordInput"
          ref="newpass"
          placeholder="new password"
          v-on:keydown.esc="state.closePrompts()"
          v-on:keydown.enter="submit()"
        >
      </div>
      <div v-else>
        <span class="smallText">enter your new password</span>
        <input
          type="password"
          v-model="newpass"
          class="renewPasswordInput"
          ref="newpass"
          placeholder="new password"
          v-on:keydown.esc="state.closePrompts()"
          v-on:keydown.shift.tab="$refs.bottomTabAnchor.focus()"
          v-on:keydown.enter="submit()"
        >
      </div>
      <br><br>
      <span class="smallText">confirm password</span>
      <input
        type="password"
        v-model="confirmPassword"
        class="renewPasswordInput"
        ref="confirmpass"
        placeholder="confirm password"
        v-on:keydown.esc="state.closePrompts()"
        v-on:keydown.tab="$refs.tabAnchor.focus()"
        v-on:keydown.shift.tab="$refs.confirmpass.focus()"
        v-on:keydown.enter="submit()"
      >
      <div style="position: absolute;font-size:.6em;color: #f448;" v-if="newpass && !validate">
        passwords don't match!
      </div>
      <br><br>
      <input
        style="position: absolute; z-index:-1; opacity: 0;z-index: -1"
        ref="bottomTabAnchor"
        type="text"
      >
      <br><br>
      <button
        @click="submit()"
        class="submitButton"
        :class="{'disabledButton': !validate}"
        :disabled="!validate"
      >submit</button>
    </div>
  </div>
</template>

<script>

export default{
  name: 'RenewPassword',
  props: [ 'state', 'item'],
  components: {
  },
  data(){
    return{
      email: '',
      newpass: '',
      confirmPassword: ''
    }
  },
  computed: {
    validate(){
      return (this.newpass || this.confirmPassword) && this.newpass==this.confirmPassword
    }
  },
  methods:{
    submit(){
      if(this.validate){
        let sendData={
          email: this.email,
          mode: this.state.renewPassMode,
          token: this.state.renewPassToken,
          newpass: this.newpass,
          pkh: this.state.renewPassPkh,
          hash: this.state.renewPassHash
        }
        console.log(sendData)
        fetch(this.state.baseURL + '/renewPassword.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          console.log(data)
          if(data[0]){
            alert('you are now logged in')
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
          } else {
            alert('there was a problem updating your password. crap.')
          }
        })
      }
    }
  },
  mounted(){
    if(this.state.renewPassEmailRequired){
      this.$refs.email.focus()
    } else {
      this.$refs.newpass.focus()
    }
  }
}
</script>

<style scoped>
.main{
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: #000d;
  position: fixed;
  top:0;
  left:0;
  font-size: 24px;
  z-index:150;
  text-align: auto;
}
.mainContainer{
  height: 400px;
  max-height: 400px;
}
.submitButton{
  position: absolute;
  bottom: 10px;
}
.smallText{
  display: block;
  text-align: left;
  font-size: .7em;
}
.disclaimerText{
  display: block;
  font-size: .5em;
}
.renewPasswordInput{
  width: 100%;
}
</style>