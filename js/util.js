function formatDate(date, fmt){
  function pad(value) {
    return (value.toString().length < 2) ? '0' + value : value;
  }
  return fmt.replace(/%([a-zA-Z])/g, function (_, fmtCode) {
    switch (fmtCode) {
      case 'Y':
        return date.getUTCFullYear();
      case 'M':
        return pad(date.getUTCMonth() + 1);
      case 'n':
        return date.getUTCMonth() + 1;
      case 'd':
        return pad(date.getUTCDate());
      case 'H':
        return pad(date.getUTCHours());
      case 'm':
        return pad(date.getUTCMinutes());
      case 's':
        return pad(date.getUTCSeconds());
      default:
        throw new Error('Unsupported format code: ' + fmtCode);
    }
  });
}


function long2ip(ip){
  //  discuss at: http://locutus.io/php/long2ip/
  // original by: Waldo Malqui Silva (http://waldo.malqui.info)
  if (!isFinite(ip)) {
    return false
  }
  return [ip >>> 24, ip >>> 16 & 0xFF, ip >>> 8 & 0xFF, ip & 0xFF].join('.')
}
