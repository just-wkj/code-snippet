// add by wkj 2018
// email: justwkj@gmail.com
// 支付宝小程序常用的代码封装
// 使用时候直接 require引入如
// var Just = require('../../util/just')

const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}
const dateFormat = (time, fmt = 'YYYY-MM-DD') => {
  let date = new Date(time)
  var o = {
    'M+': date.getMonth() + 1,
    'D+': date.getDate(),
    'h+': date.getHours() % 12 === 0 ? 12 : date.getHours() % 12,
    'H+': date.getHours(),
    'm+': date.getMinutes(),
    's+': date.getSeconds(),
    'q+': Math.floor((date.getMonth() + 3) / 3),
    'S': date.getMilliseconds()
  }
  var week = {
    '0': '\u65e5',
    '1': '\u4e00',
    '2': '\u4e8c',
    '3': '\u4e09',
    '4': '\u56db',
    '5': '\u4e94',
    '6': '\u516d'
  }
  if (/(Y+)/.test(fmt)) {
    fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length))
  }
  if (/(E+)/.test(fmt)) {
    fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? '\u661f\u671f' : '\u5468') : '') + week[date.getDay() + ''])
  }
  for (var k in o) {
    if (new RegExp('(' + k + ')').test(fmt)) {
      fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length)))
    }
  }
  return fmt
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}
//======= 设置一下常用的模块
//设置页面标题
const setPageTitle = title => {
  my.setNavigationBar({
    title
  })
}
//获取App实例
const Appx = getApp()
//获取接口实例
const Api = require('./api');
const Text = require('./text');
const Config = require('./config');

//toast 成功提示
const toastOk = (content, success) => {
  if(success){
    my.showToast({
      type: 'success',
      duration: 2000,
      content,
      success
    })
  } else {
    my.showToast({
      type: 'success',
      duration: 2000,
      content: content,
    })
  }

}

//toast 失败提示
const toastErr = (content,success) => {
  if(success){
    my.showToast({
      type: 'none',
      content: content,
      duration: 2000,
      success:success
    });
  } else {
    my.showToast({
      type: 'none',
      content: content,
      duration: 2000,
    });
  }

}

//跳转页面
const naviagteTo = url => {
  my.navigateTo({
    url: url
  })
}
const redirectTo = url => {
  my.redirectTo({
    url: url
  })
}

//跳转tab
const switchTo = url => {
  my.switchTab({
    url: url
  })
}

const goBack = () => {
  my.navigateBack()
}

const cacheGet = key => {
  return my.getStorageSync({
    key
  }).data
}
const cacheSet = (key, data) => {
  return my.setStorageSync({
    key,
    data
  });
}

const inArray = (key, array) => {
  return array.indexOf(key) >= 0
}

const deleteArray = (array, key) => {
  let i = array.indexOf(key)
  array.splice(i, 1)
  return array
}

/*获取当前页url*/
const getCurrentPageUrl = () => {
  var pages = getCurrentPages()    //获取加载的页面
  var currentPage = pages[pages.length - 1]    //获取当前页面的对象
  var url = currentPage.route    //当前页面url
  return url
}

/*获取当前页带参数的url*/
const getCurrentPageUrlWithArgs = () => {
  var pages = getCurrentPages()    //获取加载的页面
  var currentPage = pages[pages.length - 1]    //获取当前页面的对象
  var url = currentPage.route    //当前页面url
  var options = currentPage.options    //如果要获取url中所带的参数可以查看options
  //拼接url的参数
  var urlWithArgs = url + '?'
  for (var key in options) {
    var value = options[key]
    urlWithArgs += key + '=' + value + '&'
  }
  urlWithArgs = urlWithArgs.substring(0, urlWithArgs.length - 1)

  return urlWithArgs
}

//添加分享上报
const shareReport = (url) => {
  App._post_form(Api.shareReport, {
    url
  }, res => {
    console.log(res)
})
}

const bigImg = (url,urls) => {
  urls = urls||''
  if(urls){
    my.previewImage({
      current: url, // 当前显示图片的http链接
      urls: urls // 需要预览的图片http链接列表
    })
  } else {
    my.previewImage({
      current: url, // 当前显示图片的http链接
      urls: [url] // 需要预览的图片http链接列表
    })
  }
}
const elemetData = (e, key) => {
  return e.currentTarget.dataset[key]
}
const elementValue = e => {
  return e.detail.value
}

//判断空对象
const isEmptyObj = obj => {
  return Object.keys(obj).length === 0
}
const modal = (msg, confirm, cancel, title) => {
  title = title || '提示';
  my.showModal({
    title: title.toString(),
    content: msg.toString(),
    success(res) {
      if (res.confirm) {
        console.log('用户点击确定')
        if (typeof (confirm) == "function") {
          confirm();
        }
      } else if (res.cancel) {
        console.log('用户点击取消')
        if (typeof (cancel) == "function") {
          cancel();
        }
      }
    }
  })
}

const trimSpace = str => {
  if (str == null) {
    return '';
  }
  str = str.toString()
  return str.replace(/^\s+|\s+$/g, "");
}


const myGet = (url, data, success, fail) => {
  return request(url, 'GET', data, success, fail)
}
const myPost = (url, data, success, fail) => {
  return request(url, 'POST', data, success, fail)
}
const request = (url, method,data, success, fail) => {
  var headers = {
    comefrom: 1,
  }
  var cookie = cacheGet('cookie')
  if(cookie){
    headers.cookie = cookie
  }
  return my.httpRequest({
    url,
    method,
    headers,
    data,
    dataType: 'json',
    success: function(res) {
      console.log('requset success')
      switch (res.data.state) {
        case 0:
          return success && success(res.data);
        case 10001:
          toastErr(res.data.detail)
          break
        case 10002:
          toastErr(res.data.detail)
          return fail && fail(res.data);
        case 10003:
          toastErr('参数错误，id不存在')
          break
        case 10004:
          toastErr('参数错误（格式错误/传入的参数不正确）')
          break
        case 10005:
          toastErr('图片上传失败')
          break
        case 10006:
          toastErr('未获取到图片')
          break
        case 10007:
          toastErr('该用户（公司）已被注册')
          break
        case 10008:
          toastErr('该用户（公司）不存在')
          break
        case 10009:
          setGlobal('isLogin', false)
          setGlobal('userInfo', {})
          return toCompanyLoginPage(1)
          break
        case 10010:
          setGlobal('isLogin', false)
          setGlobal('userInfo', {})
          return toParterLoginPage(1)
        case 10011:
          toastErr('密码错误')
          break
        case 10012:
          toastErr('该合伙人已存在')
          break
        case 10013:
          toastErr('该合伙人不存在')
          break
        case 10014:
          //xxx     wepy.reLaunch({ url: '/pages/login/login' })
          toastErr('该用户（公司）已被禁用')
          break
        case 10015:
          //xxx  wepy.reLaunch({ url: '/pages/login/login' })
          toastErr('该合伙人已被禁用')
          break
        case 10016:
          setGlobal('isLogin', false)
          setGlobal('userInfo', {})
          return toastErr('您用户（公司）登录 超时，请重新登录',function () {
            return toCompanyLoginPage(1)
          })
          break
        case 10017:
          setGlobal('isLogin', false)
          setGlobal('userInfo', {})
          return toastErr('您（合伙人）登录超时，请重新登录',function () {
            return toParterLoginPage(1)
          })

          break
        case 20003:
          toastErr('您选购的票据中已有被其它用户抢购，请刷新购物车查看。')
          break
        default:
          return success && success(res.data);
      }
    },
    fail: function(res) {
      console.log('request fail')
    },
    complete: function(res) {
      console.log('request complete')
      // my.hideLoading();
    }
  });
}

const isLogin = () => {
  if(isEmptyObj(Appx.globalData) ||isEmptyObj(Appx.globalData.userInfo)){
    return false
  }
  return Appx.globalData.userInfo
}
const setGlobalUserInfo = (userInfo) => {
  return Appx.globalData.userInfo = userInfo
}

const toCompanyLoginPage = (redirect) => {
  redirect = redirect || false
  if(redirect){
    redirectTo('/pages/login/login')
  } else {
    naviagteTo('/pages/login/login')
  }
}
const toParterLoginPage = (redirect) => {
  redirect = redirect || false
  if(redirect){
    redirectTo('/pages/userLogin/login')
  } else {
    naviagteTo('/pages/userLogin/login')
  }
}

const getGlobalData = () => {
  return Appx.globalData
}
const globalData = () => {
  return Appx.globalData
}
const setGlobal = (variable, value) => {
  if(variable == 'account'){
    Appx.globalData.account = value
  }
  if(variable == 'userInfo'){
    Appx.globalData.userInfo = value
  }
  if(variable == 'banks'){
    Appx.globalData.banks = value
  }
  if(variable == 'days'){
    Appx.globalData.days = value
  }
  if(variable == 'moneys'){
    Appx.globalData.moneys = value
  }
  if (variable == 'score') {
    var userInfo = Appx.globalData.userInfo
    userInfo.score = value
    Appx.globalData.userInfo = userInfo
  }
  if (variable == 'isLogin') {
    Appx.globalData.isLogin = value
  }
}

//拨打电话
const callPhone = (number) => {
  my.makePhoneCall({number:number});
}

const imgUpload = (imgArr, i, callBack) => {
  my.uploadFile({
    url: Api.imageUpload,    //自己服务器接口地址
    fileType: 'image',
    fileName: 'file',
    filePath: imgArr[i],
    formData: {   //这里写自己服务器接口需要的额外参数
      // session: my.getStorageSync({key:'session'}).data
    },
    success: (res) => {
    var image = JSON.parse(res.data).data
    callBack&&callBack(image)
},
});
}

const reportFormId = from_id => {
  myPost(Api.getFormId, {from_id})
}
const reportFormId2 = from_id => {
  myPost(Api.getFormId2, {from_id})
}
const copyText = (text, success) => {
  my.setClipboard({
    text,
    success
  });
}

const getSettingInfo = (callback) => {
  var shareInfo = {}
  return myGet(Api.getSettingInfo, {}, info => {
    shareInfo.title = info.data.title
  shareInfo.imageUrl = info.data.shareImage
  cacheSet('shareInfo', shareInfo);
  callback && callback()
})
}

const share = (success,path) => {
  var shareInfo = cacheGet('shareInfo')
  if(!shareInfo){
    getSettingInfo()
  }
  var Obj = {
    title:shareInfo.title,
    imageUrl:shareInfo.imageUrl,
  }
  if(path){
    Obj.path = path
  }
  if(success){
    Obj.success = success
  }
  console.log(Obj)
  return Obj;

}

//导出设置
module.exports = {
  formatTime,
  setPageTitle,
  Appx,
  Api,
  Config,
  Text,
  toastOk,
  toastErr,
  naviagteTo,
  redirectTo,
  switchTo,
  cacheGet,
  cacheSet,
  goBack,
  inArray,
  deleteArray,
  getCurrentPageUrl,
  getCurrentPageUrlWithArgs,
  shareReport,
  bigImg,
  // post,
  // get,
  // toastErr,
  elemetData,
  elementValue,
  isEmptyObj,
  modal,
  trimSpace,
  myGet,
  myPost,
  isLogin,
  dateFormat,
  getGlobalData,
  globalData,
  toCompanyLoginPage,
  toParterLoginPage,
  callPhone,
  imgUpload,
  setGlobalUserInfo,
  reportFormId,
  reportFormId2,
  setGlobal,
  copyText,
  share
}
