<template>
  <div
    class="w-full h-full flex md:align-items-center md:justify-content-center px-4"
    style="min-height: calc(100vh - 4rem)"
  >
    <span class="text-center text">
      The
      <span class="gradient-text">NOBEL</span> prize
    </span>
  </div>
</template>

<style scoped>
.text {
  font-size: 12rem;
  font-weight: 900;
}
.gradient-text {
  font-size: 14rem;
  background: linear-gradient(
    74deg,
    rgb(66, 133, 244) 0px,
    rgb(155, 114, 203) 9%,
    rgb(217, 101, 112) 20%,
    rgb(217, 101, 112) 24%,
    rgb(155, 114, 203) 35%,
    rgb(66, 133, 244) 44%,
    rgb(155, 114, 203) 50%,
    rgb(217, 101, 112) 56%,
    rgb(217, 101, 112) 60%,
    rgb(155, 114, 203) 71%,
    rgb(66, 133, 244) 80%,
    rgb(155, 114, 203) 89%,
    rgb(217, 101, 112) 100%
  );
  color: #fff; /* White text for better contrast */
  padding: 5px; /* Add some padding for better readability */
  background-clip: text;
  animation: textShine 10s linear infinite;
  background-size: 500% auto;

  -webkit-text-fill-color: transparent;
}

@keyframes textShine {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 100% 50%;
  }
}

@media screen and (max-width: 1230px) {
  .text {
    font-size: 8.2rem;
  }
  .gradient-text {
    font-size: 10rem;
  }
}

@media screen and (max-width: 860px) {
  .text {
    font-size: 6.2rem;
  }
  .gradient-text {
    font-size: 8rem;
  }
}

@media screen and (max-width: 510px) {
  .text {
    font-size: 4.2rem;
  }
  .gradient-text {
    font-size: 6rem;
  }
}
</style>
