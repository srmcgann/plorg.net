<template>
  <div class="main" :class="{'shortHeight': state.minimized, 'normalHeight': !state.minimized}">
    <div v-if="state.search.string==''">
      <div v-if="state.mode=='user' && state.loaded" class="cardContainer">
        <div v-for="item in filteredItems(state.user.items)" class="item" :class="{'normalItem': !state.monochrome, 'monochromeItem': state.monochrome}" :key="item.id">
          <Card :state="state" :item="item"/>
        </div>
      </div>
      <div v-else-if="state.mode=='token'" class="cardContainer">
        <div v-if="state.tokenNotFound" style="font-size: 1.2em;text-align: center;">
          <br><br><br><br><br><br><br>
          404<br><br>
          awwww, this token was not found<br><br><br>
          :(
        </div>
        <div v-else>
          <div v-for="(item, idx) in filteredItems(state.items)" class="item" :class="{'normalItem': !state.monochrome, 'monochromeItem': state.monochrome}" :key="idx">
            <Card :state="state" :item="item"/>
          </div>
        </div>
      </div>
      <div v-else class="cardContainer">
        <div v-for="item in state.items" class="item" :class="{'normalItem': !state.monochrome, 'monochromeItem': state.monochrome}" :key="item.id">
          <Card :state="state" :item="item"/>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="itemDiv" :class="{'highTop':state.showControls}">
        <div v-if="state.search.items.length" class="cardContainer">
          <div v-for="(item, idx) in filteredItems(state.search.items)"  class="item" :class="{'normalItem': !state.monochrome, 'monochromeItem': state.monochrome}" :key="item.id">
            <Card :state="state" :item="item"/>
          </div>
        </div>
        <div v-if="state.search.inProgress" style="font-size: 2em;positon: absolute; width:100%;margin-top:200px;" >
          <div style="width: 300px;padding-left: 50px;margin-left: auto; margin-right: auto; text-align: left;">{{searchingText}}</div>
        </div>
        <div v-else-if="!state.search.items.length && state.loaded" style="font-size: 1.25em;margin-top:150px;text-align: center;">
          <br>DRAT!
          <br><br>your search did not return anything!
          <br><br>
          maybe try something more general,<br>like &quot;whitehot robot&quot;
          <br><br><br>
          :(
        </div>
      </div>
    </div>
    <div style="position: relative;left: 50%;margin-top:-50px;margin-bottom: 30px;transform: translate(-50%);width: 400px;text-align: center;">
      <Navigation v-if="!state.search.inProgress" :state="state"/>
    </div>
  </div>
</template>

<script>
import Card from './Card'
import Navigation from './Navigation'

export default {
  name: 'Main',
  props: [ 'state' ],
  components:{
    Card,
    Navigation
  },
  data(){
    return {
    }
  },
  methods: {
    filteredItems(items){
      return items.filter(v=>(+v.listed)||v.userHash==this.state.loggedinUserHash || v.creatorHash==this.state.loggedinUserHash)
    },
    loadUserPage(userHash){
      window.location.href=this.state.baseURL + '/u/' + this.state.users[userHash].name
    },
    cardData(item){
      let html = ''
      html += '<div class="clear"></div>'
      Object.entries(item).forEach(([key, val]) => {
        if(
            key !== 'image' &&
            key !== 'ipfs' &&
            key !== 'id' &&
            key !== 'address' &&
            key !== 'views' &&
            key !== 'royalties' &&
            key !== 'userID' &&
            key !== 'userHash' &&
            key !== 'creatorHash' &&
            key !== 'private' &&
            key !== 'metaData' &&
            key !== 'creatorID' &&
            key !== 'history' &&
            key !== 'title'
          ){
          html += '<span class="itemTitle'+(this.state.monochrome?' monchromeItemTitle':' normalItemTitle')+'">' + key + '</span>'
          html += '<div class="itemData">'+val+'</div><br>'
        }
        if(key == 'creatorHash'){
          html += '<span class="itemTitle'+(this.state.monochrome?' monchromeItemTitle':' normalItemTitle')+'">creator</span>'
          html += '<div class="itemData">'+this.state.users[val].name+'</div><br>'
        }
        if(key == 'royalties'){
          html += '<span class="itemTitle'+(this.state.monochrome?' monchromeItemTitle':' normalItemTitle')+'">royalties</span>'
          html += '<div class="itemData">'+Math.round(val*10000)/100+'%</div><br>'
        }
      })
      return html
    },
    formattedDate(d){
      let M=['January','February','March','April','May','June','July','August','September','October','November','December']
      var l=new Date(d)
      return M[l.getMonth()] + ' ' + l.getDate() + ', ' + l.getFullYear()// + ' • ' + (l.getHours()%12) + ':' + l.getMinutes() + ' ' + (l.getHours()<12?'AM':'PM')
    },
  },
  computed:{
    searchingText(){
      return 'Searching' + ('.'.repeat((this.state.globalT*20|0)%8))
    }
  },
  mounted(){
  }
}
</script>

<style>
.main{
  background-color: #0000;
  margin-top: 0;
  margin-bottom: 0;
  overflow-x: hidden;
  overflow-y: auto;
  transition: .3s height;
 }
.normalHeight{
  height: calc(100vh - 185px);
  max-height: calc(100vh - 185px);
}
.shortHeight{
  height: calc(100vh - 71px);
  max-height: calc(100vh - 71px);
}
.mainContainer{
  position: absolute;
  width: 600px;
  left: 50%;
  top: 47%;
  line-height: 1em;
  margin-top: 10px;
  font-size: 16px;
  min-width: 320px;
  min-height: 200px;
  border-radius: 10px;
  padding: 10px;
  transform: translate(-50%, -50%);
}
.normalMainContainer{
  color: #8fc;
  background-color: #024;
}
.monochromeMainContainer{
  color: #aaa;
  background-color: #333;
}
.item{
  margin: 3px;
  margin-top: 40px;
  padding: 5px;
  float: left;
  min-width:600px;  
  max-width: 700px;
  border:1px solid #fff2;
}
.normalItem{
  background: linear-gradient(-135deg, #202, #0000);
}
.monochromeItem{
  background: linear-gradient(-135deg, #111, #0000);
}
.userCard{
  margin: 0px;
  padding: 10px;
  background: #123;
}
.cardContainer{
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-evenly;
  margin-bottom: 40px;
  margin-top: -25px;
}
.avatar, .small_avatar{
  margin-left: auto;
  margin-right: auto;
  height: 150px;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
  border-radius: 50%;
  vertical-align: top;
  border: 1px solid #4fb0;
}
.small_avatar{
  display: inline-block;
  width: 40px;
  height: 40px;
  margin: 0;
  vertical-align: bottom;
  position: relative;
  transform:translate(-2px, 15px);
  margin-top: 10px;
}
.avatar{
  width: 150px;
  margin-top: -3px;
}
.cardVal{
  display: inline-block;
  max-width: 250px;
  padding-left: 4px;
}
.name{
  word-break: break-word;
  font-size: 28px;
  line-height: 1em;
  text-align: center;
  display: block;
  padding-bottom: 10px;
}
.itemData{
  font-size: 14px;
  max-width: 300px;
  text-align: left;
  display: inline-block;
  color: #fff;
  padding:0px;
}
.itl{
  min-width: 120px!important;
}
.itemTitle{
  display: inline-block;
  font-size: 14px;
  padding: 0;
  padding-left: 0px;
  padding-right: 5px;
  text-align: right;
  min-width: 100px;
  vertical-align: bottom;
}
.normalItemTitle{
  color: #aff;
  background: linear-gradient(-90deg, #025, #0000);
}
.monochromeItemTitle{
  color: #eee;
  background: linear-gradient(-90deg, #444, #0000);
}
.itemHeaderTitle, .itemHeaderDescription{
  text-align: left;
  text-shadow: 1px 1px 2px #000;
  padding:0px;
  padding-left:10px;
  line-height: 1em;
}
.itemHeaderTitle{
  font-size: 32px;
  padding-top: 5px;
  margin-bottom: 10px;
}
.normalItemHeaderTitle{
  color: #edf;
  background: linear-gradient(135deg, #044, #0000, #0000);
}
.monochromeItemHeaderTitle{
  color: #eee;
  background: linear-gradient(135deg, #333, #0000, #0000);
}
.itemHeaderDescription{
  font-size: 20px;
  font-style: oblique;
  padding: 10px;
  padding-top:0;
  padding-left: 20px;
}
.itemDetails{
  border-bottom: 1px solid #ace4;
  font-size: 14px;
  display: inline-block;
  padding: 0;
  width: calc(100% - 120px);
  padding-left: 10px;
  padding-right: 0px;
  color: #acea;
  vertical-align: bottom;
}
.clear{
  clear: both;
}
.itemImage{
  float: left;
  width: 235px;
  height: 235px;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  border: 1px solid #aff0;
  float: right;
  margin: 0;
  margin-bottom: 10px;
  vertical-align: top;
}
.fullscreenButton{
  background-image: url(https://jsbot.whitehot.ninja/uploads/1ERPZC.png);
  background-size: 100% 100%;
  min-width: initial;
  background-color: transparent;
  background-repeat: no-repeat;
  width: 50px!important;
  height: 50px!important;
  position: absolute;
  right:0;
  margin-top: 0;
  margin-right: 20px;
}
.portrait{
  background-size: contain!important;
}
.landscape{
  background-size: contain!important;
}
</style>
