function playAlarm() {
  const audio = document.getElementById("alarm-audio");
  if (audio) audio.play().catch(()=>{});
}
