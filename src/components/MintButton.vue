<template>
  <button
    @click="chooseAction()"
    class="mintButton"
    :class="{'normalButtonBG':!state.monochrome,'monochromeButtonBG':state.monochrome,'disabledButton': (mintedOut || !(+item.listed) || !(+item.enabled)) && !(state.loggedin && item.userID == state.loggedinUserID),'shortButton':(+item.listed) && state.loggedin && item.userID == state.loggedinUserID}"
    :disabled="(mintedOut || !(+item.listed) || !(+item.enabled)) && !(state.loggedin && item.userID == state.loggedinUserID)"
    :title="titleText"
  >
   <span v-if="((+item.listed) && !state.loggedin) || (+item.listed)&& state.loggedin && item.userID != state.loggedinUserID">
     <div v-if="!mintedOut">
       mint for {{item.price}} tez <span style="font-size:.7em;color: #afcb;" v-if="this.state.loggedin && !state.showAssetModal" v-html="'wallet ' + this.state.walletData.balance+'ꜩ'"></span>
     </div>
     <div v-else>
       MINTED OUT!
     </div>
   </span>
   <span v-else-if="!(+item.listed) && state.loggedin && item.userID == state.loggedinUserID">
     list this item
   </span>
   <span v-else-if="(+item.listed) && state.loggedin && item.userID == state.loggedinUserID">
     edit listing
   </span>
   <span v-else>
     item not listed
   </span>
  </button>
  <button
    title="unlist item"
    v-if="(+item.listed) && state.loggedin && item.userID == state.loggedinUserID"
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
        if(!this.item.listed && this.item.userID == this.state.loggedinUserID){
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
        passhash: this.state.passhash,
        itemID: this.item.id
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
        if(this.item.userID == this.state.loggedinUserID){
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