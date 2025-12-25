/// Job Model
class Job {
  final int id;
  final String title;
  final String description;
  final String? location;
  final double? latitude;
  final double? longitude;
  final String? type; // full-time, part-time, contract, etc.
  final String? status; // open, filled, closed
  final double? salary;
  final String? salaryType; // hourly, daily, weekly, monthly
  final int? clientId;
  final String? clientName;
  final DateTime? startDate;
  final DateTime? endDate;
  final DateTime createdAt;
  final DateTime updatedAt;
  final Map<String, dynamic>? requirements;
  final List<String>? skills;

  Job({
    required this.id,
    required this.title,
    required this.description,
    this.location,
    this.latitude,
    this.longitude,
    this.type,
    this.status,
    this.salary,
    this.salaryType,
    this.clientId,
    this.clientName,
    this.startDate,
    this.endDate,
    required this.createdAt,
    required this.updatedAt,
    this.requirements,
    this.skills,
  });

  factory Job.fromJson(Map<String, dynamic> json) {
    return Job(
      id: json['id'] as int,
      title: json['title'] as String,
      description: json['description'] as String,
      location: json['location'] as String?,
      latitude: json['latitude'] != null ? (json['latitude'] as num).toDouble() : null,
      longitude: json['longitude'] != null ? (json['longitude'] as num).toDouble() : null,
      type: json['type'] as String?,
      status: json['status'] as String? ?? 'open',
      salary: json['salary'] != null ? (json['salary'] as num).toDouble() : null,
      salaryType: json['salary_type'] as String?,
      clientId: json['client_id'] as int?,
      clientName: json['client_name'] as String?,
      startDate: json['start_date'] != null
          ? DateTime.parse(json['start_date'] as String)
          : null,
      endDate: json['end_date'] != null
          ? DateTime.parse(json['end_date'] as String)
          : null,
      createdAt: DateTime.parse(json['created_at'] as String),
      updatedAt: DateTime.parse(json['updated_at'] as String),
      requirements: json['requirements'] as Map<String, dynamic>?,
      skills: json['skills'] != null
          ? List<String>.from(json['skills'] as List)
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'location': location,
      'latitude': latitude,
      'longitude': longitude,
      'type': type,
      'status': status,
      'salary': salary,
      'salary_type': salaryType,
      'client_id': clientId,
      'client_name': clientName,
      'start_date': startDate?.toIso8601String(),
      'end_date': endDate?.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'requirements': requirements,
      'skills': skills,
    };
  }
}

