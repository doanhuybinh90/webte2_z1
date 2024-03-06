<?php
include '../config.php';

session_start();

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
    $sql = "SELECT r.id as id, p.year, cr.country_name as country, c.category, r.name, r.surname, pd.language_sk, pd.language_en, pd.genre_sk, pd.genre_en, p.contribution_sk, p.contribution_en, r.organisation, r.birth, r.death 
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
    $sql = "SELECT r.id as id, p.year, cr.country_name as country, c.category, r.name, r.surname
                FROM prizes p
                JOIN categories c ON p.category_id = c.id
                JOIN receivers r ON p.receiver_id = r.id
                JOIN countries cr ON r.country_id = cr.id
                WHERE r.organisation = ''
                ";
    if ($filterYearValue) {
      $sql .= " AND p.year = '$filterYearValue'";
    }
    if ($filterCategoryValue) {
      $sql .= sprintf(" AND c.category = '$filterCategoryValue'");
    }
    $sql .= " ORDER BY $sortKey $sortMethod";
    $sql .= " LIMIT $limit";
    $sql .= " OFFSET " . ($page - 1) * $limit;
  }
  $result = $conn->query($sql);
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
  

  $sql_total = "SELECT COUNT(*) as total FROM prizes p JOIN receivers r ON p.receiver_id = r.id WHERE r.organisation = ''";
  $result_total = $conn->query($sql_total);

  $sql_unique_years = "SELECT DISTINCT year FROM prizes ORDER BY year ASC";
  $result_unique_years = $conn->query($sql_unique_years);
  $years = array();
  while ($row = $result_unique_years->fetch_assoc()) {
      $years[] = $row['year'];
  }

  $sql_unique_categories = "SELECT DISTINCT category FROM categories ORDER BY category ASC";
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
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {

  $data = json_decode(file_get_contents('php://input'), true);
  $name = $data['name'];
  $surname = $data['surname'];
  $birth = $data['birth'];
  $death = $data['death'];
  $organisation = $data['organisation'];
  $country = $data['country'];
  $contribution_sk = $data['contribution_sk'];
  $contribution_en = $data['contribution_en'];
  $genre_sk = $data['genre_sk'];
  $genre_en = $data['genre_en'];
  $language_sk = $data['language_sk'];
  $language_en = $data['language_en'];
  $category = $data['category'];
  $year = $data['year'];
  $sex = $data['sex'];

  // country exists ? 
  $sql = "SELECT * FROM countries WHERE country_name = '$country'";
  $result = $conn->query($sql);
  if ($result->num_rows === 0) {
    $sql = "INSERT INTO countries (country_name) VALUES ('$country')";
    $conn->query($sql);
  }

  // receiver exists ?
  $receiver_id = "NULL";
  $sql = "SELECT * FROM receivers WHERE ((name = '$name' AND surname = '$surname') OR (organisation = '$organisation')) AND (country_id = (SELECT id FROM countries WHERE country_name = '$country'))";
  $result = $conn->query($sql);
  if ($result->num_rows === 0) {
    $sql_receivers = "INSERT INTO receivers (name, surname, organisation, birth, death, sex, country_id) VALUES ('$name', '$surname', '$organisation', '$birth', '$death', '$sex', (SELECT id FROM countries WHERE country_name = '$country'))";
    $conn->query($sql_receivers);
    $receiver_id = "$conn->insert_id";
  } else {
    $row = $result->fetch_assoc();
    $receiver_id = $row['id'];
  }


  $prize_details_id = "NULL";
  if($category == 'Literature') {
    $sql_prize_details = "INSERT INTO prize_details (genre_sk, genre_en, language_sk, language_en) VALUES ('$genre_sk', '$genre_en', '$language_sk', '$language_en')";
    $conn->query($sql_prize_details);
    $prize_details_id  = "$conn->insert_id";
  }

  $sql_prizes = sprintf("
    INSERT INTO prizes (
      year, 
      category_id, 
      receiver_id, 
      prize_details_id, 
      contribution_sk, 
      contribution_en
    ) VALUES (
      '$year', 
      (SELECT id FROM categories WHERE category = '$category'), 
      $receiver_id, 
      $prize_details_id, 
      '$contribution_sk', 
      '$contribution_en'
    )");

  $conn->query($sql_prizes);

  header('Content-Type: application/json');
  echo json_encode(['success' => false, 'isOrganisation' => empty($organisation) ? false : true, 'receiver_id' => $receiver_id]);
} else {
  echo json_encode(['error' => 'Method not supported']);
}



$conn->close();
