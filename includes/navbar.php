<!-- navbar.php -->
<aside class="w-64 h-full border-r border-gray-200 bg-white px-6 py-8 flex flex-col">
  <h1 class="text-xl font-bold mb-8 flex items-center gap-2 text-gray-800">
    <i data-lucide="folder-kanban" class="w-5 h-5"></i> FM25
  </h1>

  <!-- หมวด: ระบบหลัก -->
  <p class="text-xs uppercase text-gray-400 mb-2 px-1">ระบบหลัก</p>
  <nav class="flex flex-col gap-2 text-sm text-gray-700">
    <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="layout-dashboard" class="w-4 h-4"></i> แดชบอร์ด
    </a>
    <a href="manage_players.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="users" class="w-4 h-4"></i> จัดการนักเตะ
    </a>
    <a href="position.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="file-badge" class="w-4 h-4"></i> ตำแหน่ง
    </a>
    <a href="stats.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="chart-spline" class="w-4 h-4"></i> จัดการคะแนนนักเตะ
    </a>
    <a href="academy.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="graduation-cap" class="w-4 h-4"></i> อาคาเดมี่
    </a>
  </nav>

  <!-- หมวด: ข้อมูลและสถิติ -->
  <p class="text-xs uppercase text-gray-400 mt-6 mb-2 px-1">ข้อมูล & สถิติ</p>
  <nav class="flex flex-col gap-2 text-sm text-gray-700">
    <a href="analyze.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="bot" class="w-4 h-4"></i> AI วิเคราะห์นักเตะ
    </a>
    <a href="statistics.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="bar-chart-3" class="w-4 h-4"></i> สถิตินักเตะ
    </a>
    <a href="team_report.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="folder-cog" class="w-4 h-4"></i> รายงานสรุปทีม
    </a>
  </nav>

  <!-- หมวด: ประวัติ -->
  <p class="text-xs uppercase text-gray-400 mt-6 mb-2 px-1">ประวัติ</p>
  <nav class="flex flex-col gap-2 text-sm text-gray-700">
    <a href="trophies.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="award" class="w-4 h-4"></i> ความสำเร็จ
    </a>
    <a href="formor_players.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="clock" class="w-4 h-4"></i> นักเตะในอดีต
    </a>
  </nav>


  <!-- Logout -->
  <div class="mt-auto border-t border-gray-200 pt-6">
    <a href="settings.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100">
      <i data-lucide="settings" class="w-4 h-4"></i> ตั้งค่า
    </a>
    <a href="logout.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-red-500">
      <i data-lucide="log-out" class="w-4 h-4"></i> ออกจากระบบ
    </a>
  </div>
</aside>