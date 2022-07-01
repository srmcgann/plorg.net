<template>
  <div class="card" v-if="typeof state.users[item.userHash] != 'undefined'">
    <div class="itemHeaderTitle" :class="{'normalItemHeaderTitle':!state.monochrome, 'monochromeItemHeaderTitle': state.monochrome}">
      <div>
        {{item.title}}
      </div>
      <div class="itemHeaderDescription">
        {{item.description}}
      </div>
    </div>
    <div style="float: left;display: inline-block; min-width: 300px;">
      <div @click="state.launchItem(item)" class="itemImage" :style="'cursor: pointer;background-image: url(' + item.image + ');'"></div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">card link</span>
        <div class="cardVal">
          <a
            :href="state.baseURL+'/t/'+state.decToAlpha(item.id)"
            @click="loadToken(item.hash)"
            class="slug"
            :class="{'normalSlug': !state.monochrome,'monochromeSlug': state.monochrome}"
            v-html="slug()"
          ></a></div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">asset</span>
        <div class="cardVal">
          <span
            @click="loadDirectAsset(item)"
            class="slug"
            :class="{'normalSlug': !state.monochrome,'monochromeSlug': state.monochrome}"
            v-html="'link'"
          ></span></div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">views</span>
        <div class="cardVal">{{item.views}}</div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">minted</span>
        <div class="cardVal">{{processedTimestamp(item.date)}}</div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">created</span>
        <div class="cardVal">{{processedTimestamp(item.created)}}</div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">hash</span>
        <div class="cardVal" style="font-size:.7em;">{{item.hash}}</div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">edition</span>
        <div class="cardVal">{{((+item.mints)<(+item.editions)?((+item.originalContent)||(+item.listed)?(+item.mints+1)+'/'+item.editions:(item.edition)):item.editions+'/'+item.editions+' [minted out!]')}}</div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">price</span>
        <div class="cardVal">{{item.price}}ꜩ</div>
      </div>
      <div class="clear"></div>
      <div class="itemData">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">royalties</span>
        <div class="cardVal">{{item.royalties}}%</div>
      </div>
    </div>
    <div style="max-width: 260px;float: right;width: 260px;display: inline-block;padding: 0px;margin:0;">
      <div 
        style="cursor: pointer;  background: #0000;border-radius: 0px;padding:5px;padding-top: 5px;"
      >
        <div style="text-align: center;margin-bottom: 10px;margin-top:-10px;">this token owner</div>
				<div
          class="avatar"
          title="load user page"
          @click="loadUserPage(item.userHash)"
          :style="'background-image: url(' + state.users[item.userHash].avatar + ');'"
        ></div>
        <span class="name" v-html="state.users[item.userHash].name"></span>
        <div class="pkh" :class="{'highlight':addresscopied}" title="click to copy tezos address" @click="copyAddress()">{{state.users[item.userHash].pkh}}</div><br>
        <div style="font-size: .8em;">
          <span class="itemTitle itl" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">joined</span>
          <span class="itemData">
            &nbsp;{{processedTimestamp(state.users[item.userHash].joined, 1)}}
          </span>
          <br>
          <span class="itemTitle itl" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">tokens created</span>
          <span class="itemData">
            &nbsp;{{state.users[item.userHash].numCreated}}
          </span>
          <br>
          <span class="itemTitle itl" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">tokens owned</span>
          <span class="itemData">
            &nbsp;{{state.users[item.userHash].numOwned}}
          </span>
        </div>
      </div>
      <div class="spacerDiv" style="margin-top: 10px;margin-bottom: 20px;"></div>
          <div v-if="(+item.originalContent)" class="marketSegment originalContent">
            THIS TOKEN IS IN<br>
            THE <b>PRIMARY MARKET</b>
          </div>
          <div v-else class="marketSegment secondaryMarket">
            THIS TOKEN IS IN<br>
            THE <b>SECONDARY MARKET</b>
          </div>
      </div>
      <div class="itemData itemRoyalties" @click="loadUserPage(item.creatorHash)" style="display: inline-block;cursor: pointer;min-width:200px;min-width: 55%;max-width: calc(100% - 10px);margin-bottom:0px;position: relative;transform: translatey(-20px);">
        <span class="itemTitle" :class="{'normalItemTitle':!state.monochrome,'monochromeItemTitle':state.monochrome}">creator</span>
          <div
            class="small_avatar"
            :style="'background-image: url(' + (state.users[item.creatorHash] ? state.users[item.creatorHash].avatar : '') + ');margin-left: 10px;'"
          ></div>
          <div style="display: inline-block;margin-top: 10px;margin-left: 5px;word-break: break-all;max-width:calc(100% - 165px);width: 500px;">
          {{state.users[item.creatorHash] ? state.users[item.creatorHash].name : ''}}</div>
      </div>
      <button
        @click="state.launchItem(item)"
        class="launchButton"
        :class="{'normalButtonBG':!state.monochrome,'monochromeButtonBG':state.monochrome}"

        style="margin-top: 0px;"
      >
       launch
      </button>
      <MintButton :state="state" :item="item"/>
    <div v-if="0" class="itemCard" v-html="cardData(item)"></div>
    <div class="commentContainer">
      <div class="commentsHeader" :class="{'normalCommentsHeader': !state.monochrome, 'monochromeCommentsHeader': state.monochrome}">
        comments
      </div>
      <div v-if="state.loggedin">
        <input
          maxlength="512"
          v-on:keyup.enter="postComment(item.hash, item.id)"
          :id="'newComment'+item.hash" placeholder="say something..."
          class="commentInput newComment textInput"
          :class="{'normalCommentInput':!state.monochrome,'monochromeCommentInput':state.monochrome}"
          style="margin-left: 0;margin-top: 16px; border: 1px solid #123f;"
        >
        <button
          @click="postComment(item.hash, item.id)"
          :class="{'normalButtonBG':!state.monochrome,'monochromeButtonBG':state.monochrome}"
          style="color:#fff;padding: 2px;font-size: 1em;padding-bottom: 4px;margin: 0;width: 55px;display: block; margin-top: 17px; min-width: initial; padding: 0; float:right;"
        >post</button>
        <div style="clear:both"></div>
      </div>
      <div v-if="item.comments.length">
        <div v-for="(comment, idx) in filteredComments">
          <div class="commentMain" :class="{'useBorder': 1||idx}">
                <div 
                  @click="loadUserPage(comment.userHash)"
                  :class="{'bumpUp': state.userAgent.toUpperCase().indexOf('FIREFOX')!==-1}"
                  class="commentAvatar" :style="'background-image:url('+state.getCommentAvatar(comment.userHash)+');width:50px!important;height:50px!important;background-repeat: no-repeat; background-position: center center; background-size: cover;position: relative;float: left;'"></div>
            <div style="float:left;max-width: calc(100% - 52px);width:calc(100% - 52px);">
            <span  v-if="typeof state.users[comment.userHash] != 'undefined'" class="commentUserName" style="font-size: 20px;">
              <span class="timestamp" v-html="processedTimestamp(comment.date)" style="float: right;display: inline-block!important;"></span>
                <a :href="state.baseURL + '/u/' + state.users[comment.userHash].name" style="color:#4dc!important;font-style: oblique;margin-left: 5px;float: left;margin-top: 10px;">{{state.users[comment.userHash].name}}</a>
              </span>
              <div v-if="comment.editing && state.loggedin" style="display:inline-block;width:calc(100% + 30px);">
                <input
                  maxlength="512"
                  type="text"
                  placeholder="say something..."
                  :id="'comment'+comment.hash"
                  class="commentInput textInput"
                  style="width: calc(100% - 100px);"
                  @input="editComment(comment)"
                  v-model="comment.text"
                  :class="{'success':comment.updated==1,'failure':comment.updated==-1}"
                >
                <button
                  v-if="comment.userHash == state.loggedinUserHash || state.isAdmin"
                  @click='comment.editing = !comment.editing'
                  class="commentEditButton"
                  style="min-width: 0;margin:0;margin-top:-1px;height: 25px;left: 0;"
                ></button>
                <button
                  @click='deleteComment(comment, item)'
                  class="commentDeleteButton"
                  style="min-width: 0;margin:0;margin-top:-1px;height: 25px;left: 0;"
                ></button>
              </div>
              <div v-else style="display: inline-block;width: calc(100% + 10px);left: 0!important;padding-right: 0;">
                <span class="commentText" :class="{'normalCommentText':!state.monochrome,'monochromeCommentText':state.monochrome}" v-html="comment.text" v-linkified style="width: calc(100% - 68px)"></span>
                <button
                  v-if="comment.userHash == state.loggedinUserHash || state.isAdmin"
                  @click='toggleEditMode(comment)'
                  class="commentEditButton"
                  style="min-width: 0;margin:0;margin-top:-1px;height: 25px;left: 0;display: inline-block;background-image:url(https://jsbot.cantelope.org/uploads/2cyWBg.png);"
                ></button>
              </div>
            </div>
          </div>
            <div style="clear:both;"></div>
        </div>
        <div v-if="moreComments" style="text-align: center;">
          <span style="display: inline-block;font-size: 32px;">...</span>
          <button
            class = "loadCommentsButton"
            @click="incrComments()"
          >load more comments</button>
        </div>
      </div>
      <div v-else><br>
        <div style="text-align: center;">-- no comments --</div>
        <div v-if="!state.loggedin" style="text-align: center;">(log in to comment on this item)</div>
      </div>
    </div>
  </div>
</template>

<script>
import MintButton from './MintButton'

export default {
  name: 'Card',
  props: [ 'state', 'item' ],
  components: {
    MintButton
  },
  data(){
    return {
      showComments: 0,
      addresscopied: false
    }
  },
  methods:{
    doAddressClick(){
      this.clicking=true
      setTimeout(()=>{this.clicking=false},200)
    },
    slug(){
      return '/' + this.state.decToAlpha(this.item.id)
    },
    loadDirectAsset(item){
      let el = document.createElement('a')
      el.href = this.state.baseIPFSURL + '/' + item.ipfs
      el.target = '_blank'
      el.click()
    },
    copyAddress(){
      if(window.navigator.userAgent.toUpperCase().indexOf('FIREFOX')!=-1){
        var text = this.item.address;
        navigator.clipboard.writeText(text).then(function() {
          console.log('Async: Copying to clipboard was successful!');
        }, function(err) {
          console.error('Async: Could not copy text: ', err);
        });
      } else{
        var range = document.createRange()
        let el = document.createElement('div')
        el.innerHTML = this.item.address
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
      }
      setTimeout(()=>{
        this.addresscopied = false
      }, 250)
    },
    incrComments(){
      this.showComments += this.moreCommentsVal
    },
		toggleEditMode(comment){
			comment.editing = !comment.editing
			if(comment.editing){
				this.$nextTick(()=>{
			    document.querySelector('#comment'+comment.hash).focus()
			    document.querySelector('#comment'+comment.hash).select()
				})
			}
		},
    deleteComment(comment, item){
			if(confirm('are you SURE you want to delete this comment?!?!?\n\n\nTHIS ACTION IS IRREVERSIBLE!')){
        let hash = comment.hash
        let sendData = {
          userName: this.state.loggedinUserName,
          passhash: this.state.passhash,
          commentHash: comment.hash
        }
        fetch(this.state.baseURL + '/deleteComment.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(data[0]){
            item.comments = item.comments.filter(v=>v.hash != hash)
          }
        })
		  }
    },
		editComment(comment){
			let hash = comment.hash
			let sendData = {
        userName: this.state.loggedinUserName,
        comment: comment.text,
        passhash: this.state.passhash,
        commentHash: hash
      }
      fetch(this.state.baseURL + '/updateComment.php',{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
      .then(res => res.json())
      .then(data => {
        if(data[0]){
          comment.updated = 1
          setTimeout(()=>comment.updated = 0, 1000)
        } else {
          comment.updated = -1
          setTimeout(()=>comment.updated = 0, 1000)
        }
      })
		},
    postComment(hash, id){
      let text = document.querySelector('#newComment'+hash).value.substring(0, 512)
			if(text.length){
        let sendData = {
          userName: this.state.loggedinUserName,
          userHash: this.state.loggedinUserHash,
          itemID: id,
          comment: text,
          passhash: this.state.passhash,
          itemHash: hash
        }
        fetch(this.state.baseURL + '/postComment.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res => res.json())
        .then(data => {
          if(data[0]){
		  			let comment = {
			  			userID: this.state.loggedinUserID,
			  			userHash: this.state.loggedinUserHash,
				  		text,
					  	itemHash: hash,
					  	itemID: id,
              id: data[1],
  						date: data[2],
              updated: false,
              editing: false
	  				}
            this.state.fetchUserData(this.state.loggedinUserHash)

            if(this.state.search.string){
              this.state.search.items.filter(v=>v.hash==hash)[0].comments=[comment,...this.state.search.items.filter(v=>v.hash==hash)[0].comments]
            }else{
              switch(this.state.mode){
                case 'user':
                this.state.user.items.filter(v=>v.hash==hash)[0].comments=[comment,...this.state.user.items.filter(v=>v.hash==hash)[0].comments]
                break
                case 'default':
                this.state.items.filter(v=>v.hash==hash)[0].comments=[comment,...this.state.items.filter(v=>v.hash==hash)[0].comments]
                break
                case 'token':
                this.state.items.filter(v=>v.hash==hash)[0].comments=[comment,...this.state.items.filter(v=>v.hash==hash)[0].comments]
                break
              }        
            }

						document.querySelector('#newComment'+hash).value = ''
			  	}
        })
			}
    },
    loadUserPage(userHash){
      window.location.href = 'https://' + this.state.rootDomain + '/u/' + this.state.users[userHash].name
    },
    loadToken(hash){
      window.location.href = 'https://' + this.state.rootDomain + '/t/' + this.state.decToAlpha(id)
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
            key !== 'date' &&
            key !== 'price' &&
            key !== 'edition' &&
            key !== 'editions' &&
            key !== 'created' &&
            key !== 'mints' &&
            key !== 'enabled' &&
            key !== 'comments' &&
            key !== 'description' &&
            key !== 'views' &&
            key !== 'royalties' &&
            key !== 'originalContent' &&
            key !== 'userID' &&
            key !== 'private' &&
            key !== 'listed' &&
            key !== 'metaData' &&
            key !== 'creatorID' &&
            key !== 'creatorHash' &&
            key !== 'userHash' &&
            key !== 'history' &&
            key !== 'title'
          ){
          html += '<span class="itemTitle">' + key + '</span>'
          html += '<div class="itemDetails">'+val+'</div><br>'
        }
      })
      return html
    },
    formattedDate(d){
      let M=['January','February','March','April','May','June','July','August','September','October','November','December']
      var l=new Date(d)
      return M[l.getMonth()] + ' ' + l.getDate() + ', ' + l.getFullYear()// + ' • ' + (l.getHours()%12) + ':' + l.getMinutes() + ' ' + (l.getHours()<12?'AM':'PM')
    },
    processedTimestamp(val, short=false){
      if(!val) return
      let months=[
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
      ]
      /*
      let days=[
        'Sun,',
        'Mon,',
        'Tue,',
        'Wed,',
        'Thur,',
        'Fri,',
        'Sat,'
      ]
      */
      let days = Array(7).fill('')
      let hours = [
        12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11
      ]
      let d = new Date(Date.parse(val.replace(/-/g, '/')))
      let mn = '' + d.getMinutes()
      if(mn.length == 1) mn = '0' + mn
      if(short){
        return days[d.getDay()] + months[d.getMonth()] + ' ' + d.getDate() + ' ' + d.getFullYear()
      }else{
        return hours[d.getHours()] + ':' + mn + (d.getHours<12?'AM':'PM') + ' ' + days[d.getDay()] + months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear()
      }
    }
  },
  mounted(){
    this.showComments = this.state.mode == 'token' ? 50 : 3
    //if((this.state.autolaunchTokens || this.item.type.indexOf('audio/')!==-1) && this.state.mode=='token') this.state.launchItem(this.item)
  },
  computed:{
    moreCommentsVal(){
      return this.state.mode == 'token' ? 50 : 25
    },
    filteredComments(){
      return this.item.comments.filter((v,i)=>i<this.showComments)
    },
    moreComments(){
      return this.item.comments.length > this.showComments
    },
    searchingText(){
      return 'Searching' + ('.'.repeat((this.state.globalT*20|0)%8))
    }
  }
}
</script>
<style scoped>
.card{
  margin-top: 0;
}
.success{
  background: #264!important;
}
.timestamp{
  margin-top: 15px;
  font-size: .7em;
}
</style>
<style>
.useBorder{
	border-bottom: 2px solid #48f3;
}
.pkh{
  border-radius: 5px;
  padding:1px;
  padding-left: 10px!important;
  padding-right: 10px!important;
  margin-left: -10px;
  margin-top:-10px;
  font-size:.7em;
  position: relative;
  z-index: 20;
}
.highlight{
  background: #4fa;
  color: #021;
}
.slug{
  padding: 0px;
  padding-left: 10px;
  padding-right: 10px;
  text-shadow: 1px 1px 2px #000;
  color: #fff;
  margin-left: 10px;
  border-radius: 5px;
  cursor: pointer;
  line-height: 1em;
}
.normalSlug{
  background: #2a6;
}
.monochromeSlug{
  background: #444;
}
.marketSegment{
  margin-left: -10px;
  width: 260px;
  height: 45px;
  border: 1px solid #fff2;
  text-align: center;
  padding-top:5px;
  text-shadow: 2px 2px 2px #000;
}
.originalContent{
  background: #4c64;
  color: #afc;
}
.secondaryMarket{
  background: #c664;
  color: #fbb;
}
</style>
