# Project Name

## Getting Started

To run this Symfony REST API locally, follow the steps below:

### 1. Configure Environment Variables

Before running the project, create a `.env` file by copying `.env.example`:

Then, fill in the required environment variables:

- `DATAFORSEO_USER`
- `DATAFORSEO_PASSWORD`
- `OPENAI_API_KEY`

These variables are necessary for the application to function properly. Without them, requests will not work.

### 2. Start the Symfony Local Server

If Symfony is already installed, you can start the local server by running:

```sh
  symfony server:start
```

If Symfony is not installed, follow the official documentation to set it up: [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html)

### 3. Make a Request

- Curl example on how to make a request to get visibility metrics:

```sh
  curl --location 'http://127.0.0.1:8000/api/visibility-metrics' \
--header 'Content-Type: application/json' \
--data '{
    "domain": "seomonitor.com",
    "keywords": [
        "topic seomonitor",
        "seo keyword monitor",
        "seo monitor login",
        "seo monitor forecast",
        "seo forecasting tool",
        "rank monitor",
        "seo rankings monitor",
        "serp features monitor",
        "seo forecasting",
        "agency rank tracking",
        "forecasting seo",
        "monitor keywords",
        "seo monitoring software",
        "rank tracker features",
        "daily rank tracker",
        "serp visibility",
        "seo monitoring tool",
        "seo visibility search metrics",
        "serp chrome extension",
        "track serp features",
        "what is rank tracking",
        "keyword forecasting",
        "rank tracker keyword difficulty",
        "seo visibility score",
        "serp metrics",
        "serp feature tracker"
    ]
}'
```

- Curl example on how to make a request to get insights:

```sh
  curl --location 'http://127.0.0.1:8000/api/seo-insights' \
--header 'Content-Type: application/json' \
--data '{
    "domain": "seomonitor.com",
    "keywords": [
        "topic seomonitor",
        "seo keyword monitor",
        "seo monitor login",
        "seo monitor forecast",
        "seo forecasting tool",
        "rank monitor",
        "seo rankings monitor",
        "serp features monitor",
        "seo forecasting",
        "agency rank tracking",
        "forecasting seo",
        "monitor keywords",
        "seo monitoring software",
        "rank tracker features",
        "daily rank tracker",
        "serp visibility",
        "seo monitoring tool",
        "seo visibility search metrics",
        "serp chrome extension",
        "track serp features",
        "what is rank tracking",
        "keyword forecasting",
        "rank tracker keyword difficulty",
        "seo visibility score",
        "serp metrics",
        "serp feature tracker"
    ]
}'
```


---

## API Endpoints

### 1. Calculate Visibility Metrics

- **Endpoint:** `POST /api/visibility-metrics` (example: http://127.0.0.1:8000/api/visibility-metrics)
- **Description:** This endpoint calculates visibility metrics based on the provided domain and keywords.
- **Request Payload:**

  ```json
  {
    "domain": "example.com",
    "keywords": ["seo monitor login", "topic seomonitor"]
  }
  ```
- **Response Example:**

  ```json
  {
    "totals": {
      "total_vscore": 7.51,
      "count": 26,
      "total_search_volume": 8110
    },
    "list": [
      {
        "name": "seo monitor login",
        "position": "0",
        "vscore": 0,
        "search_volume": 70
      },
      {
        "name": "topic seomonitor",
        "position": "5",
        "vscore": 0.7,
        "search_volume": 170
      }
    ]
  }
  ```

---

### 2. Generate SEO Insights

- **Endpoint:** `POST /api/seo-insights` (example: http://127.0.0.1:8000/api/seo-insights)
- **Description:** This endpoint calculates visibility metrics for a domain and its keywords, then sends the calculated metrics to OpenAI API to generate SEO insights.
- **Request Payload:**

  ```json
  {
    "domain": "example.com",
    "keywords": ["seo monitor login", "topic seomonitor"]
  }
  ```
- **Response Example:**

  ```json
  {
    "insights": [
      {
        "insight": "High search volume keywords with poor rankings present an opportunity for improvement.",
        "keywords": "rank monitor, daily rank tracker, monitor keywords, seo visibility score, what is rank tracking",
        "ranking_impact": "High"
      },
      {
        "insight": "Keywords with high rankings but low visibility scores indicate under performance in search results.",
        "keywords": "seo monitor login, serp chrome extension, rank tracker keyword difficulty, serp features monitor, serp feature tracker, serp metrics, keyword forecasting",
        "ranking_impact": "Medium"
      }
    ],
    "metrics": {
      "totals": {
        "total_vscore": 7.51,
        "count": 26,
        "total_search_volume": 8110
      },
      "list": [
        {
          "name": "seo monitor login",
          "position": "0",
          "vscore": 0,
          "search_volume": 70
        },
        {
          "name": "topic seomonitor",
          "position": "5",
          "vscore": 0.7,
          "search_volume": 170
        }
      ]
    }
  }
  ```

---

## Additional Notes
- The current requests are extremely slow. I think this is due to the rate-limiter package installed and limitations added on requests. There might be a more performant way of handling multiple requests at once, but due to time constraints and considering this is just a test project, I didn't go deeper into understanding and fixing the performance issues.
- There is also a lot of room for improvement, such as adding DTOs to responses, rearranging some services in a better way, and making further optimizations.
