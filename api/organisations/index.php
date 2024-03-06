<?php
include '../config.php';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = $_GET['id'];
  $page = $_GET['page'];
  $limit = $_GET['limit'];
  $sortKey = $_GET['sortKey'];
  $sortMethod = $_GET['sortMethod'];
  $filterYearValue = $_GET['filterYearValue'];
  $filterCategoryValue = $_GET['filterCategoryValue'];

  if ($id) {
    $sql = "SELECT r.id as id, p.year, cr.country_name as country, c.category, r.name, r.surname, pd.language_sk, pd.language_en, pd.genre_sk, pd.genre_en, p.contribution_sk, p.contribution_en, r.organisation 
                FROM prizes p
                LEFT JOIN prize_details pd ON p.prize_details_id = pd.id
                JOIN categories c ON p.category_id = c.id
                JOIN receivers r ON p.receiver_id = r.id
                JOIN countries cr ON r.country_id = cr.id 
                WHERE r.id = $id
                ORDER BY p.year ASC, p.id ASC
                ";
  } else {
    if (!$page) {
      $page = 1;
    }
    if (!$limit) {
      $limit = 10;
    }
    if (!$sortKey) {
      $sortKey = 'year';
    }
    if (!$sortMethod) {
      $sortMethod = 'ASC';
    }
    $sql = "SELECT r.id as id, p.year, cr.country_name as country, c.category, r.organisation
                FROM prizes p
                JOIN categories c ON p.category_id = c.id
                JOIN receivers r ON p.receiver_id = r.id
                JOIN countries cr ON r.country_id = cr.id";
    if ($filterYearValue) {
      $sql .= " WHERE p.year = '$filterYearValue'";
    }
    if ($filterCategoryValue) {
      $sql .= sprintf(" %s c.category = '$filterCategoryValue'", $filterYearValue ? 'AND' : 'WHERE');
    }
    $sql .= sprintf(" %s r.organisation <> ''", $filterYearValue || $filterCategoryValue ? 'AND' : 'WHERE');

    $sql .= " ORDER BY $sortKey $sortMethod";
    $sql .= " LIMIT $limit";
    $sql .= " OFFSET " . ($page - 1) * $limit;
  }

  $result = mysqli_query($conn, $sql);
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);


  
  $sql_total = "SELECT COUNT(*) as total FROM prizes p JOIN receivers r ON p.receiver_id = r.id WHERE r.organisation <> ''";
  $result_total = $conn->query($sql_total);

  $sql_unique_years = "SELECT DISTINCT year FROM prizes p JOIN receivers r ON p.receiver_id = r.id WHERE r.organisation <> '' ORDER BY year ASC";
  $result_unique_years = $conn->query($sql_unique_years);
  $years = array();
  while ($row = $result_unique_years->fetch_assoc()) {
      $years[] = $row['year'];
  }

  $sql_unique_categories = "SELECT DISTINCT c.category FROM prizes p JOIN categories c ON p.category_id = c.id JOIN receivers r ON p.receiver_id = r.id  WHERE r.organisation <> ''  ORDER BY category ASC";
  $result_unique_categories = $conn->query($sql_unique_categories);
  $categories = array();
  while ($row = $result_unique_categories->fetch_assoc()) {
      $categories[] = $row['category'];
  }

  header('Content-Type: application/json; charset=utf-8');
  $response = [
    'data' => $rows,
    'total' => $result_total->fetch_assoc()['total'],
    'uniqueYears' =>  $years, 
    'uniqueCategories' => $categories, 
  ];
  echo json_encode($response);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo "POST";
  echo $_POST("id");
  // $sql = "INSERT INTO receivers (name, birthdate, birthplace, country) VALUES ('" . $data['name'] . "', '" . $data['birthdate'] . "', '" . $data['birthplace'] . "', '" . $data['country'] . "')";
  // $conn->query($sql);
} else {
  echo json_encode(['error' => 'Method not supported']);
}



$conn->close();
