

ແນ່ນອນ, ນີ້ຄືຕາຕະລາງສະຫຼຸບ Models ທັງໝົດ 60 ອັນ ຕາມໂຄງສ້າງທີ່ທ່ານຕ້ອງການ,
ໂດຍອີງໃສ່ສິ່ງທີ່ເຮົາໄດ້ສົນທະນາ ແລະ ສ້າງກັນມາ:

  -----------------------------------------------------------------------------------------------------------
  **ລະຫັດ        **ຊື່ Model (ທີ່ແນະນຳ/ຕົກລົງ)**  **Model ທີ່ກ່ຽວຂ້ອງຫຼັກ          **ລາຍລະອຽດ**          **ໝາຍເຫດ**
  Datastore**                              (ຕົວຢ່າງ)**                                        
  ------------- -------------------------- -------------------------- --------------------- -----------------
  D1            User                       Role, Student, Teacher,    ຂໍ້ມູນຜູ້ໃຊ້ລະບົບທັງໝົດ        Core Model,
                                           StudentParents,                                  ຖືກອ້າງອີງຫຼາຍ
                                           UserActivity, etc.                               

  D2            Role                       User, Permission           ຂໍ້ມູນບົດບາດຜູ້ໃຊ້           Master Data

  D3            Permission                 Role                       ຂໍ້ມູນສິດທິການໃຊ້ງານ        Master Data

  D4            (ບໍ່ມີ Model ສະເພາະ)          Role, Permission           ຕາຕະລາງເຊື່ອມຕໍ່ (Pivot)  ໃຊ້ຜ່ານ
                                                                                            belongsToMany ໃນ
                                                                                            Role/Permission

  D5            UserActivity               User                       ບັນທຶກກິດຈະກຳຜູ້ໃຊ້         Log Table

  D6            BiometricData              User, BiometricLog         ຂໍ້ມູນຊີວະມິຕິ              

  D7            BiometricLog               User, BiometricData        ບັນທຶກການໃຊ້ຊີວະມິຕິ        Log Table

  D8            Province                   District, Student,         ຂໍ້ມູນແຂວງ               Master Data
                                           Teacher, StudentParents,                         
                                           etc.                                             

  D9            District                   Province, Village,         ຂໍ້ມູນເມືອງ               Master Data
                                           Student, Teacher,                                
                                           StudentParents, etc.                             

  D10           Village                    District, Student,         ຂໍ້ມູນບ້ານ                Master Data
                                           Teacher, StudentParents,                         
                                           etc.                                             

  D11           Nationality                Student                    ຂໍ້ມູນສັນຊາດ              Master Data

  D12           Religion                   Student                    ຂໍ້ມູນສາສະໜາ             Master Data

  D13           Ethnicity                  Student                    ຂໍ້ມູນຊົນເຜົ່າ              Master Data

  D14           Student                    User, Nationality,         ຂໍ້ມູນນັກຮຽນ              Core Model, ມີ
                                           Religion, Ethnicity,                             Relationships ຫຼາຍ
                                           Village, District,                               
                                           Province, StudentParents,                        
                                           StudentEnrollment, Grade,                        
                                           etc.                                             

  D15           StudentPreviousLocation    Student, Village,          ທີ່ຢູ່ເກົ່ານັກຮຽນ            
                                           District, Province                               

  D16           StudentInterest            Student                    ຄວາມສົນໃຈນັກຮຽນ         

  D17           StudentSibling             Student (x2)               ພີ່ນ້ອງນັກຮຽນ             Pivot Table
                                                                                            (Self-ref)

  D18           StudentDocument            Student                    ເອກະສານນັກຮຽນ          

  D19           StudentHealthRecord        Student                    ຂໍ້ມູນສຸຂະພາບນັກຮຽນ        

  D20           StudentEmergencyContact    Student                    ຜູ້ຕິດຕໍ່ສຸກເສີນ             

  D21           StudentPreviousEducation   Student                    ປະຫວັດການສຶກສາກ່ອນໜ້າ     

  D22           StudentAchievement         Student                    ຜົນງານ/ລາງວັນນັກຮຽນ      

  D23           StudentBehaviorRecord      Student, Teacher           ບັນທຶກພຶດຕິກຳນັກຮຽນ        

  D24           StudentSpecialNeed         Student                    ຄວາມຕ້ອງການພິເສດ        

  D25           StudentParents             User, Village, District,   ຂໍ້ມູນຜູ້ປົກຄອງ             ໃຊ້ຊື່ Model ນີ້ຕາມຄຳຂໍ
                                           Province, Student                                

  D26           StudentParent              Student, StudentParents    ເຊື່ອມໂຍງນັກຮຽນ-ຜູ້ປົກຄອງ   Pivot Table

  D27           Teacher                    User, Village, District,   ຂໍ້ມູນຄູສອນ               Core Model
                                           Province, SchoolClass,                           
                                           Schedule, etc.                                   

  D28           TeacherDocument            Teacher                    ເອກະສານຄູສອນ           

  D29           AcademicYear               SchoolClass,               ຂໍ້ມູນສົກຮຽນ              Master Data
                                           StudentEnrollment,                               
                                           Examination, etc.                                

  D30           SchoolClass                AcademicYear, Teacher,     ຂໍ້ມູນຫ້ອງຮຽນ             ໃຊ້ຊື່ Model ນີ້ແທນ
                                           StudentEnrollment,                               Classes
                                           Subject, Schedule, etc.                          

  D31           Subject                    SchoolClass, Grade,        ຂໍ້ມູນວິຊາຮຽນ             Master Data
                                           Schedule, Attendance, etc.                       

  D32           ClassSubject               SchoolClass, Subject,      ເຊື່ອມໂຍງ ຫ້ອງ-ວິຊາ-ຄູ     Pivot Table
                                           Teacher                                          (ມີຂໍ້ມູນເພີ່ມ)

  D33           StudentEnrollment          Student, SchoolClass,      ການລົງທະບຽນນັກຮຽນ       
                                           AcademicYear                                     

  D34           Attendance                 Student, SchoolClass,      ການຂາດ-ມາຮຽນ (ຂໍ້ມູນດິບ)  Log Table
                                           Subject, User                                    

  D35           Schedule                   SchoolClass, Subject,      ຕາຕະລາງສອນ            
                                           Teacher, AcademicYear                            

  D36           Examination                AcademicYear, Grade        ການສອບເສັງ             

  D37           Grade                      Student, SchoolClass,      ຄະແນນນັກຮຽນ            
                                           Subject, Examination, User                       

  D38           StudentAttendanceSummary   Student, SchoolClass,      ສະຫຼຸບການຂາດ-ມາ         Summary Table
                                           AcademicYear                                     (ສ້າງຈາກ D34)

  D39           FeeType                    StudentFee                 ປະເພດຄ່າທຳນຽມ          Master Data

  D40           StudentFee                 Student, FeeType,          ລາຍການຄ່າທຳນຽມນັກຮຽນ    
                                           AcademicYear, Payment                            

  D41           Payment                    StudentFee, Student, User  ການຊຳລະເງິນ            Transaction Table

  D42           Discount                   StudentDiscount            ປະເພດສ່ວນຫຼຸດ            Master Data

  D43           StudentDiscount            Student, Discount,         ການມອບສ່ວນຫຼຸດໃຫ້ນັກຮຽນ    
                                           AcademicYear, User                               

  D44           Expense                    User                       ລາຍຈ່າຍໂຮງຮຽນ          

  D45           Income                     User                       ລາຍຮັບໂຮງຮຽນ (ອື່ນໆ)     

  D46           ReportTemplate             User, GeneratedReport      ແມ່ແບບລາຍງານ           

  D47           GeneratedReport            ReportTemplate, User       ລາຍງານທີ່ສ້າງແລ້ວ         Log Table

  D48           Message                    User (x2)                  ຂໍ້ຄວາມພາຍໃນລະບົບ        

  D49           Announcement               User                       ປະກາດຂ່າວສານ           

  D50           Notification               User, (Polymorphic)        ການແຈ້ງເຕືອນ            Log Table

  D51           Request                    User (x2)                  ຄຳຮ້ອງຕ່າງໆ             

  D52           SchoolStoreItem            StoreSale                  ສິນຄ້າໃນຮ້ານຄ້າ           Master Data

  D53           StoreSale                  SchoolStoreItem, User,     ການຂາຍຮ້ານຄ້າ           Transaction Table
                                           (Polymorphic Buyer)                              

  D54           DigitalLibraryResource     User,                      ຊັບພະຍາກອນຫ້ອງສະໝຸດ      
                                           DigitalResourceAccess                            

  D55           DigitalResourceAccess      DigitalLibraryResource,    Log ການເຂົ້າໃຊ້ຫ້ອງສະໝຸດ   Log Table
                                           User                                             

  D56           ExtracurricularActivity    User, AcademicYear,        ກິດຈະກຳນອກຫຼັກສູດ         
                                           StudentActivity                                  

  D57           StudentActivity            ExtracurricularActivity,   ການເຂົ້າຮ່ວມກິດຈະກຳ       Pivot Table
                                           Student                                          (ມີຂໍ້ມູນເພີ່ມ)

  D58           Setting                    \-                         ການຕັ້ງຄ່າລະບົບ           Key-Value Store

  D59           Backup                     User                       ປະຫວັດການ Backup       Log Table

  D60           SystemLog                  User                       Log ການເຮັດວຽກລະບົບ     Log Table
  -----------------------------------------------------------------------------------------------------------
