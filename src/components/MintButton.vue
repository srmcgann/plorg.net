<template>
  <button
    @click="chooseAction()"
    class="mintButton"
    :class="{'normalButtonBG':!state.monochrome,'monochromeButtonBG':state.monochrome,'disabledButton': (mintedOut || !(+item.listed) || !(+item.enabled)) && !(state.loggedin && item.userHash == state.loggedinUserHash),'shortButton':(+item.listed) && state.loggedin && item.userHash == state.loggedinUserHash}"
    :disabled="(mintedOut || !(+item.listed) || !(+item.enabled)) && !(state.loggedin && item.userHash == state.loggedinUserHash)"
    :title="titleText"
  >
   <span v-if="((+item.listed) && !state.loggedin) || (+item.listed)&& state.loggedin && item.userHash != state.loggedinUserHash">
     <div v-if="!mintedOut">
       mint for {{item.price}} tez <span style="font-size:.7em;color: #afcb;" v-if="this.state.loggedin && !state.showAssetModal" v-html="'wallet ' + this.state.walletData.balance+'ꜩ'"></span>
     </div>
     <div v-else>
       MINTED OUT!
     </div>
   </span>
   <span v-else-if="!(+item.listed) && state.loggedin && item.userHash == state.loggedinUserHash">
     list this item
   </span>
   <span v-else-if="(+item.listed) && state.loggedin && item.userHash == state.loggedinUserHash">
     edit listing
   </span>
   <span v-else>
     item not listed
   </span>
  </button>
  <button
    title="unlist item"
    v-if="(+item.listed) && state.loggedin && item.userHash == state.loggedinUserHash"
    @click="unlist()"
    :class="{'normalButtonBG':!state.monochrome,'monochromeButtonBG':state.monochrome}"
    class="mintButton shortButton"
  >unlist</button>
</template>

<script>

export default{
  name: 'MintButton',
  props: [ 'state', 'item' ],
  data(){
    return {
    }
  },
  computed:{
    mintedOut(){
      return (+this.item.mints)>=(+this.item.editions)
    },
    titleText(){
      if(this.state.loggedin){
        if(!this.item.listed && this.item.userHash == this.state.loggedinUserHash){
          return 'list item'
        } else {
          if(this.item.listed){
            return 'edit listing'
          }
        }
      } else {
        if(this.item.listed){
          return 'mint token'
        }
      }
    }
  },
  methods:{
    unlist(){
      let sendData = {
        pkh: this.state.walletData.address,
        userID: this.state.loggedinUserID,
        userHash: this.state.loggedinUserHash,
        passhash: this.state.passhash,
        itemID: this.item.id,
        itemHash: this.item.hash
      }
      fetch(this.state.baseURL + '/unlist.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          this.item.listed=false
        }
      })
    },
    chooseAction(){
      if(this.state.loggedin){
        if(this.item.userHash == this.state.loggedinUserHash){
          this.state.doListItem(this.state.displayItem=this.item)
        } else {
          if(this.item.listed){
            this.state.purchaseToken(this.state.displayItem=this.item)
          } 
        }
      } else {
        if(this.item.listed){
          this.state.purchaseToken(this.state.displayItem = this.item)
        }
      }
    }
  }
}
</script>

<style scoped>
.mintButton{
}
.shortButton{
  max-width: calc(25% - 15px)!important;
}
</style>