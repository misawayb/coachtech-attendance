```mermaid

erDiagram
    users {
        bigint id PK
        varchar name
        varchar email
        varchar password
        timestamp email_verified_at
        boolen admin_status
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    attendance_records{
        bigint id PK
        bigint user_id FK
        date date
        time clock_in
        time clock_out
        varchar comment
        timestamp created_at
        timestamp updated_at
    }

    attendance_breaks{
        bigint id PK
        bigint attendance_records_id FK
        time break_in
        time break_out
        timestamp created_at
        timestamp updated_at
    }

    attendance_correct_requests{
        bigint id PK
        bigint attendance_records_id FK
        time clock_in
        time clock_out
        varchar comment
        enum status
        bigint approved_by FK
        timestamp created_at
        timestamp updated_at
    }

    attendance_correct_breaks{
        bigint id PK
        bigint attendance_records_id FK
        time break_in
        time break_out
        timestamp created_at
        timestamp updated_at
    }

users ||--o{ attendance_records : "hasMany/belongsTo"
users ||--o{ attendance_correct_requests : "hasMany/belongsTo"
attendance_records ||--o{ attendance_breaks : "hasMany/belongsTo"
attendance_records ||--o{ attendance_correct_requests : "hasMany/belongsTo"
attendance_correct_requests ||--o{ attendance_correct_breaks: "hasMany/belongsTo"