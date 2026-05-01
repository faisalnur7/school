#!/usr/bin/env python3
import os
import time
import unittest
from datetime import datetime

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager


BASE_URL = os.environ.get("SELENIUM_BASE_URL", "http://127.0.0.1:8000")
LOGIN_EMAIL = os.environ.get("SELENIUM_EMAIL", "admin@example.com")
LOGIN_PASSWORD = os.environ.get("SELENIUM_PASSWORD", "password")
STUDENT_ID = os.environ.get("SELENIUM_STUDENT_ID", "")
ARTIFACT_DIR = os.environ.get("SELENIUM_ARTIFACT_DIR", "artifacts/selenium")


class AccountingAndFeesSeleniumTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        os.makedirs(ARTIFACT_DIR, exist_ok=True)

        options = Options()
        options.add_argument("--headless=new")
        options.add_argument("--no-sandbox")
        options.add_argument("--disable-dev-shm-usage")
        options.add_argument("--window-size=1600,1200")

        service = Service(ChromeDriverManager().install())
        cls.driver = webdriver.Chrome(service=service, options=options)
        cls.wait = WebDriverWait(cls.driver, 20)

    @classmethod
    def tearDownClass(cls):
        if getattr(cls, "driver", None):
            cls.driver.quit()

    def tearDown(self):
        # Save screenshot on failure for quick debugging
        outcome = getattr(self, "_outcome", None)
        if not outcome:
            return
        errors = []
        result = outcome.result
        if result:
            errors.extend(result.errors)
            errors.extend(result.failures)
        for test, exc_info in errors:
            if exc_info and test is self:
                ts = datetime.now().strftime("%Y%m%d_%H%M%S")
                path = os.path.join(ARTIFACT_DIR, f"FAIL_{self._testMethodName}_{ts}.png")
                self.driver.save_screenshot(path)

    def login(self):
        self.driver.get(f"{BASE_URL}/login")
        # If already authenticated, /login may redirect away.
        if "/login" not in self.driver.current_url:
            return

        self.wait.until(EC.presence_of_element_located((By.ID, "email"))).send_keys(LOGIN_EMAIL)
        self.driver.find_element(By.ID, "password").send_keys(LOGIN_PASSWORD)

        # Button can be rendered by Blade component without fixed id.
        submit = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        submit.click()

        self.wait.until(lambda d: "/login" not in d.current_url)
        self.assertNotIn("/login", self.driver.current_url)

    def pick_first_non_empty_option(self, select_name: str):
        sel = Select(self.wait.until(EC.presence_of_element_located((By.NAME, select_name))))
        for option in sel.options:
            if option.get_attribute("value"):
                sel.select_by_value(option.get_attribute("value"))
                return
        self.fail(f"No selectable option found for {select_name}")

    def test_01_create_income_entry(self):
        self.login()
        self.driver.get(f"{BASE_URL}/incomes/create")

        self.pick_first_non_empty_option("income_category_id")
        self.driver.find_element(By.NAME, "title").send_keys("Selenium Income Test")
        self.driver.find_element(By.NAME, "amount").send_keys("123.45")
        self.driver.find_element(By.NAME, "income_date").send_keys(datetime.now().strftime("%d/%m/%Y"))

        payment_sel = Select(self.driver.find_element(By.ID, "paymentMethod"))
        payment_sel.select_by_visible_text("Cash")

        self.driver.find_element(By.CSS_SELECTOR, "button.btn.btn-success").click()

        self.wait.until(lambda d: "/incomes" in d.current_url)
        self.wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
        body = self.driver.find_element(By.TAG_NAME, "body").text
        self.assertIn("Selenium Income Test", body)
        self.assertIn("123.45", body)

    def test_02_create_expense_entry(self):
        self.login()
        self.driver.get(f"{BASE_URL}/expenses/create")

        self.pick_first_non_empty_option("expense_category_id")
        self.driver.find_element(By.NAME, "title").send_keys("Selenium Expense Test")
        self.driver.find_element(By.NAME, "amount").send_keys("55.25")
        self.driver.find_element(By.NAME, "expense_date").send_keys(datetime.now().strftime("%d/%m/%Y"))

        payment_sel = Select(self.driver.find_element(By.ID, "expensePaymentMethod"))
        payment_sel.select_by_visible_text("Cash")

        self.driver.find_element(By.CSS_SELECTOR, "button.btn.btn-success").click()

        self.wait.until(lambda d: "/expenses" in d.current_url)
        self.wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
        body = self.driver.find_element(By.TAG_NAME, "body").text
        self.assertIn("Selenium Expense Test", body)
        self.assertIn("55.25", body)

    def test_03_reports_pages_load(self):
        self.login()
        report_paths = [
            "/reports/trial-balance",
            "/reports/balance-sheet",
            "/reports/income-expenditure",
            "/reports/cash-book",
            "/reports/day-book",
            "/reports/cash-summary",
            "/reports/receipt-payment",
            "/reports/cash-flow",
            "/reports/headwise-transactions",
        ]

        for path in report_paths:
            self.driver.get(f"{BASE_URL}{path}")
            self.wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
            self.assertNotIn("404", self.driver.title)

    def test_04_student_fees_module_page_loads(self):
        self.login()

        if not STUDENT_ID:
            self.skipTest("No SELENIUM_STUDENT_ID provided")

        self.driver.get(f"{BASE_URL}/fees/collect_payment/{STUDENT_ID}")
        self.wait.until(EC.presence_of_element_located((By.ID, "mainTabs")))

        body = self.driver.find_element(By.TAG_NAME, "body").text
        self.assertIn("Collect Payment", body)
        self.assertIn("Payment History", body)


if __name__ == "__main__":
    unittest.main(verbosity=2)
